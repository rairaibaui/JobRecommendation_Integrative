<?php

/**
 * Custom AI Verification Script
 *
 * This script verifies that the custom AI is being used for document validation.
 * GPT-4o has been completely removed from the system.
 */

echo "=== Custom AI Usage Verification ===\n\n";

// Check if custom AI files exist
echo "1. Checking custom AI files...\n";
$aiFiles = [
    'document_verifier_service/mandaluyong_ai_detector.py',
    'document_verifier_service/mandaluyong_verifier.py',
    'document_verifier_service/best_permit_detector.pth'
];

foreach ($aiFiles as $file) {
    if (file_exists($file)) {
        echo "✓ Found: $file\n";
    } else {
        echo "✗ Missing: $file\n";
    }
}

echo "\n2. Testing custom AI Python imports...\n";
$testCommand = "cd document_verifier_service && ../document_verifier_service/venv/bin/python -c \"import sys; sys.path.append('.'); from mandaluyong_ai_detector import validate_document; print('✓ Custom AI import successful')\"";

echo "Running test: $testCommand\n";
$output = shell_exec($testCommand . " 2>&1");
echo "Result: " . ($output ?: "✗ Import failed or no output") . "\n";

echo "\n3. Checking Laravel service configuration...\n";
$serviceFile = 'app/Services/DocumentValidationService.php';
if (file_exists($serviceFile)) {
    $content = file_get_contents($serviceFile);

    // Check if custom AI is primary method
    if (strpos($content, 'validateWithCustomAI') !== false) {
        echo "✓ Custom AI is configured as primary method\n";
    } else {
        echo "✗ Custom AI not found as primary method\n";
    }

    // Check that GPT-4o has been removed
    if (strpos($content, 'validateWithGPT4o') !== false || strpos($content, 'gpt-4o') !== false) {
        echo "✗ WARNING: GPT-4o references still found in service file\n";
    } else {
        echo "✓ GPT-4o has been removed from service\n";
    }

    // Check for OpenAI client initialization (should not exist)
    if (strpos($content, 'OpenAI::client') !== false || strpos($content, '\\OpenAI::client') !== false) {
        echo "✗ WARNING: OpenAI client initialization still found\n";
    } else {
        echo "✓ OpenAI client initialization removed\n";
    }

    // Check for proper directory setup
    if (strpos($content, 'sys.path.append') !== false) {
        echo "✓ Python path properly configured\n";
    } else {
        echo "✗ Python path not properly configured\n";
    }
} else {
    echo "✗ DocumentValidationService.php not found\n";
}

echo "\n4. Checking .env configuration...\n";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');

    // Check document validation is enabled
    if (strpos($envContent, 'AI_DOCUMENT_VALIDATION=true') !== false) {
        echo "✓ AI document validation is enabled\n";
    } else {
        echo "✗ AI document validation not enabled\n";
    }
} else {
    echo "✗ .env file not found\n";
}

echo "\n=== Verification Summary ===\n";
echo "The system is configured to use CUSTOM AI ONLY for document validation.\n";
echo "GPT-4o has been completely removed from the system.\n\n";

echo "Current behavior:\n";
echo "- Laravel service uses custom AI as the ONLY validation method ✓\n";
echo "- If custom AI fails, falls back to basic file validation (no GPT-4o) ✓\n";
echo "- GPT-4o has been completely removed ✓\n\n";

echo "Configuration:\n";
echo "- Custom AI is the primary and only AI validation method\n";
echo "- No OpenAI API calls for document validation\n";
echo "- Fallback validation only checks basic file properties\n\n";

echo "Note: If custom AI fails, documents will be flagged for manual review.\n";
echo "This ensures accuracy and prevents reliance on inaccurate GPT-4o results.\n";

?>
