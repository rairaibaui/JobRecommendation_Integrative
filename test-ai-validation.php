<?php

/**
 * Test AI Validation Service
 * 
 * This script tests if the AI validation service is properly configured
 * and can be called successfully.
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\DocumentValidationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

echo "=== AI Validation Service Test ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "1. Checking Python environment...\n";
$pythonPath = base_path('document_verifier_service/venv/bin/python');
$workingDir = base_path('document_verifier_service');

if (file_exists($pythonPath)) {
    echo "✓ Python virtual environment found at: {$pythonPath}\n";
} else {
    echo "✗ Python virtual environment NOT found at: {$pythonPath}\n";
    echo "  Please ensure the virtual environment is set up correctly.\n";
    exit(1);
}

if (is_dir($workingDir)) {
    echo "✓ Working directory found at: {$workingDir}\n";
} else {
    echo "✗ Working directory NOT found at: {$workingDir}\n";
    exit(1);
}

echo "\n2. Testing Python imports...\n";
$testImport = "cd " . escapeshellarg($workingDir) . " && " . escapeshellarg($pythonPath) . " -c \"import sys; sys.path.append('.'); from mandaluyong_ai_detector import validate_document; print('✓ New AI detector import successful')\" 2>&1";
$output = shell_exec($testImport);
echo "Output: " . ($output ?: "✗ Import failed or no output") . "\n";

$testImportLegacy = "cd " . escapeshellarg($workingDir) . " && " . escapeshellarg($pythonPath) . " -c \"import sys; sys.path.append('.'); from mandaluyong_verifier import verify_document; print('✓ Legacy verifier import successful')\" 2>&1";
$outputLegacy = shell_exec($testImportLegacy);
echo "Output: " . ($outputLegacy ?: "✗ Legacy verifier import failed or no output") . "\n";

echo "\n3. Testing DocumentValidationService instantiation...\n";
try {
    $service = app(DocumentValidationService::class);
    echo "✓ DocumentValidationService instantiated successfully\n";
} catch (\Exception $e) {
    echo "✗ Failed to instantiate DocumentValidationService: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n3. Testing Flask service connectivity...\n";
$baseUrl = config('services.document_verifier.base_url', 'http://localhost:5010');
$healthUrl = rtrim($baseUrl, '/') . '/health';
try {
    $response = Http::timeout(5)->get($healthUrl);
    if ($response->successful()) {
        $healthData = $response->json();
        echo "✓ Flask service is running and healthy!\n";
        echo "  Service: " . ($healthData['service'] ?? 'unknown') . "\n";
        echo "  Port: " . ($healthData['port'] ?? 'unknown') . "\n";
    } else {
        echo "⚠ Flask service returned status: " . $response->status() . "\n";
        echo "  Make sure the Flask service is running: cd document_verifier_service && FLASK_PORT=5010 python app.py\n";
    }
} catch (\Exception $e) {
    echo "✗ Cannot connect to Flask service at {$baseUrl}\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "  Make sure the Flask service is running: cd document_verifier_service && FLASK_PORT=5010 python app.py\n";
}

echo "\n4. Testing AI validation with a sample file (if available)...\n";
// Look for a sample business permit file in the uploads directory
$sampleDir = storage_path('app/public/business_permits');
if (is_dir($sampleDir)) {
    $files = glob($sampleDir . '/*/*.pdf');
    if (!empty($files)) {
        $sampleFile = $files[0];
        echo "Testing with sample file: " . basename($sampleFile) . "\n";
        
        // Convert absolute path to relative path for the service
        $relativePath = str_replace(storage_path('app/public/'), '', $sampleFile);
        
        try {
            echo "Attempting AI validation via Flask service...\n";
            $result = $service->validateBusinessPermit($relativePath, [
                'company_name' => 'Test Company',
                'email' => 'test@example.com',
            ]);
            
            echo "✓ AI validation completed successfully!\n";
            echo "Result:\n";
            echo "  - Valid: " . ($result['valid'] ? 'Yes' : 'No') . "\n";
            echo "  - Confidence: " . ($result['confidence'] ?? 0) . "%\n";
            echo "  - Requires Review: " . ($result['requires_review'] ? 'Yes' : 'No') . "\n";
            echo "  - Reason: " . ($result['reason'] ?? 'N/A') . "\n";
            if (isset($result['ai_analysis'])) {
                echo "  - AI Analysis: Available\n";
                if (isset($result['ai_analysis']['ai_model_used'])) {
                    echo "  - AI Model: " . $result['ai_analysis']['ai_model_used'] . "\n";
                }
            }
        } catch (\Exception $e) {
            echo "✗ AI validation failed: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    } else {
        echo "⚠ No sample PDF files found in {$sampleDir}\n";
        echo "  Upload a business permit to test the validation.\n";
    }
} else {
    echo "⚠ Sample directory not found: {$sampleDir}\n";
    echo "  Upload a business permit to test the validation.\n";
}

echo "\n=== Test Complete ===\n";
echo "\nIf all checks passed, the AI validation service should be working correctly.\n";
echo "If you see errors, check:\n";
echo "  1. Python virtual environment is activated and dependencies are installed\n";
echo "  2. The document_verifier_service directory is accessible\n";
echo "  3. Required Python packages are installed (check requirements.txt)\n";
echo "  4. Laravel storage permissions are correct\n";

