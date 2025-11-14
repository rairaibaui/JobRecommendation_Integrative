<?php

/**
 * Custom AI Fix - Manual Code Changes
 *
 * File: app/Services/DocumentValidationService.php
 * Function: callDocumentVerifierService (around lines 595-631)
 *
 * Replace the existing function with this fixed version:
 */

protected function callDocumentVerifierService(string $filePath): array
{
    // First try the new custom AI detector (fine-tuned vision model)
    try {
        $pythonPath = base_path('document_verifier_service/venv/bin/python');
        $workingDir = base_path('document_verifier_service');

        // Add the working directory to the Python path and import the module
        $command = "cd {$workingDir} && {$pythonPath} -c \"import sys; sys.path.append('.'); from mandaluyong_ai_detector import validate_document; import json; result = validate_document('{$filePath}'); print(json.dumps(result))\"";

        $output = shell_exec($command);

        if ($output) {
            $result = json_decode($output, true);
            if ($result && isset($result['ai_detection'])) {
                // Successfully got result from new AI detector
                return $this->convertNewAIDetectorResult($result);
            }
        }
    } catch (\Exception $e) {
        Log::info('New AI detector not available, falling back to legacy verifier: ' . $e->getMessage());
    }

    // Fallback to the legacy verifier (OCR-based)
    try {
        $pythonPath = base_path('document_verifier_service/venv/bin/python');
        $workingDir = base_path('document_verifier_service');

        $command = "cd {$workingDir} && {$pythonPath} -c \"import sys; sys.path.append('.'); from mandaluyong_verifier import verify_document; import json; result = verify_document('{$filePath}'); print(json.dumps(result))\"";

        $output = shell_exec($command);

        if ($output) {
            $result = json_decode($output, true);
            if ($result) {
                return $result;
            }
        }
    } catch (\Exception $e) {
        Log::error('Legacy verifier also failed: ' . $e->getMessage());
    }

    throw new \Exception('Could not call document verifier service');
}

/**
 * SPECIFIC CHANGES NEEDED:
 *
 * 1. Around line 599, replace:
 *    $pythonPath = base_path('document_verifier_service/venv/bin/python');
 *
 *    With:
 *    $pythonPath = base_path('document_verifier_service/venv/bin/python');
 *    $workingDir = base_path('document_verifier_service');
 *
 * 2. Around line 601, replace:
 *    $command = escapeshellcmd("{$pythonPath} -c \"from mandaluyong_ai_detector import validate_document; import json; result = validate_document('{$filePath}'); print(json.dumps(result))\"");
 *
 *    With:
 *    $command = "cd {$workingDir} && {$pythonPath} -c \"import sys; sys.path.append('.'); from mandaluyong_ai_detector import validate_document; import json; result = validate_document('{$filePath}'); print(json.dumps(result))\"";
 *
 * 3. Around line 617, replace:
 *    $scriptPath = base_path('document_verifier_service/mandaluyong_verifier.py');
 *
 *    With:
 *    try {  // Add this try block
 *        $pythonPath = base_path('document_verifier_service/venv/bin/python');
 *        $workingDir = base_path('document_verifier_service');
 *
 *        $command = "cd {$workingDir} && {$pythonPath} -c \"import sys; sys.path.append('.'); from mandaluyong_verifier import verify_document; import json; result = verify_document('{$filePath}'); print(json.dumps(result))\"";
 *
 *        $output = shell_exec($command);
 *
 *        if ($output) {
 *            $result = json_decode($output, true);
 *            if ($result) {
 *                return $result;
 *            }
 *        }
 *    } catch (\Exception $e) {
 *        Log::error('Legacy verifier also failed: ' . $e->getMessage());
 *    }
 *
 * 4. Remove the old fallback code (around lines 619-628):
 *    $command = escapeshellcmd("{$pythonPath} -c \"from mandaluyong_verifier import verify_document; import json; result = verify_document('{$filePath}'); print(json.dumps(result))\"");
 *    // ... rest of old code
 */

/**
 * EXPLANATION OF THE FIX:
 *
 * The issue was that Python couldn't find the modules because:
 * 1. The working directory wasn't set to the document_verifier_service folder
 * 2. The current directory wasn't added to Python's sys.path
 *
 * The fix:
 * 1. Adds $workingDir variable to store the full path
 * 2. Uses "cd {$workingDir} &&" to change directory before running Python
 * 3. Adds "import sys; sys.path.append('.');" to make modules importable
 * 4. Removes escapeshellcmd() which was causing path issues
 * 5. Wraps both try blocks in proper exception handling
 *
 * This ensures custom AI modules can be imported successfully,
 * so custom AI will be used instead of falling back to GPT-4o.
 */

?>
