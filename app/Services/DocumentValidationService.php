<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class DocumentValidationService
{
    public function __construct()
    {
        // Custom AI only - no OpenAI client needed
    }

    /**
     * Validate if uploaded file is a legitimate business permit using custom AI.
     *
     * @param string $filePath Path to the uploaded file in storage
     * @param array  $metadata Additional context (company name, etc.)
     *
     * @return array Validation result
     */
    public function validateBusinessPermit(string $filePath, array $metadata = []): array
    {
        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);

        // Use custom AI as the ONLY validation method - retry up to 3 times
        $maxRetries = 3;
        $lastException = null;
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info("AI validation attempt {$attempt}/{$maxRetries} for file: {$filePath}");
                return $this->validateWithCustomAI($filePath, $metadata);
            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning("AI validation attempt {$attempt}/{$maxRetries} failed: " . $e->getMessage());
                
                // If not the last attempt, wait a bit before retrying
                if ($attempt < $maxRetries) {
                    sleep(2); // Wait 2 seconds before retry
                    continue;
                }
            }
        }
        
        // All attempts failed - log the error with full details and throw exception
        $errorMessage = "AI validation failed after {$maxRetries} attempts. Last error: " . ($lastException ? $lastException->getMessage() : 'Unknown error');
        Log::error("Custom AI validation failed completely: {$errorMessage}");
        Log::error("File path: {$filePath}");
        Log::error("Full exception: " . ($lastException ? $lastException->getTraceAsString() : 'No exception'));
        
        // Instead of falling back to non-AI validation, throw an exception
        // The job handler will catch this and create a pending_review record
        throw new \Exception($errorMessage . ". AI service is required for document validation.");
    }


    /**
     * Fallback validation when AI is not available.
     */
    protected function fallbackValidation(string $filePath, array $metadata): array
    {
        // Basic file validation
        $fileSize = Storage::disk('public')->size($filePath);
        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);

        // Check if file is not empty
        if ($fileSize < 1024) { // Less than 1KB
            return [
                'valid' => false,
                'confidence' => 100,
                'reason' => 'File is too small to be a valid business permit.',
                'requires_review' => false,
                'ai_analysis' => null,
            ];
        }

        // For PDFs, try to check if it has content
        if ($mimeType === 'application/pdf') {
            // Basic PDF validation - just check if it's a valid PDF
            $content = Storage::disk('public')->get($filePath);
            if (strpos($content, '%PDF') !== 0) {
                return [
                    'valid' => false,
                    'confidence' => 100,
                    'reason' => 'File is not a valid PDF document.',
                    'requires_review' => false,
                    'ai_analysis' => null,
                ];
            }
        }

        // Without AI, we flag for manual review
        return [
            'valid' => false,
            'confidence' => 0,
            'reason' => 'AI validation is not available. Document requires manual review by administrator.',
            'requires_review' => true,
            'ai_analysis' => null,
        ];
    }


    /**
     * Validate using custom AI (new vision model or legacy OCR-based) for all business permits.
     */
    protected function validateWithCustomAI(string $filePath, array $metadata = []): array
    {
        try {
            $fullPath = Storage::disk('public')->path($filePath);

            // Call the document verifier service (tries new AI first, falls back to legacy)
            $result = $this->callDocumentVerifierService($fullPath);

            // Check if this is from the new AI detector or legacy verifier
            $extractedData = $result['extracted_data'] ?? [];
            $isNewAIDetector = isset($extractedData['ai_detection_results']);

            if ($isNewAIDetector) {
                // Process new AI detector results
                return $this->processNewAIDetectorResult($result, $metadata);
            } else {
                // Process legacy verifier results
                return $this->processLegacyVerifierResult($result, $metadata);
            }

        } catch (\Exception $e) {
            Log::error('Custom AI validation error: ' . $e->getMessage());
            throw $e; // Re-throw to allow fallback validation
        }
    }

    /**
     * Process results from the new AI detector (vision model).
     */
    protected function processNewAIDetectorResult(array $result, array $metadata): array
    {
        $extractedData = $result['extracted_data'] ?? [];
        $aiDetection = $extractedData['ai_detection_results'] ?? [];
        $summary = $aiDetection['summary'] ?? [];

        // Determine validity based on AI detection
        $isValid = $result['status'] === 'PENDING_MANUAL_REVIEW';
        $confidenceScore = $summary['confidence_score'] ?? 0;
        $confidence = (int)($confidenceScore * 100); // Convert to percentage

        // Build comprehensive AI analysis for the new detector
        $aiAnalysis = [
            'document_type' => $extractedData['document_type'] ?? 'Business Permit',
            'has_official_seals' => ($aiDetection['has_mandaluyong_logo']['present'] ?? false) ||
                                   ($aiDetection['has_signatures']['present'] ?? false),
            'has_signature' => $aiDetection['has_signatures']['present'] ?? false,
            'has_registration_number' => false, // New AI doesn't extract registration numbers yet
            'business_name_matches' => $this->checkBusinessNameMatchFromNewAI($aiDetection, $metadata),
            'appears_authentic' => $summary['is_valid_permit'] ?? false,
            'is_expired' => false, // New AI doesn't check expiry yet
            'issuing_authority' => 'Mandaluyong City Government', // Assumed for detected permits
            'validity_dates' => null, // New AI doesn't extract dates yet
            'recommendation' => $isValid ? 'MANUAL_REVIEW' : 'REJECT',
            'red_flags' => $this->extractRedFlagsFromNewAI($aiDetection),
            // New AI specific fields
            'detected_elements' => $extractedData['detected_elements'] ?? [],
            'missing_elements' => $extractedData['missing_elements'] ?? [],
            'ai_model_used' => 'mandaluyong_ai_detector_vision_model',
            'training_accuracy' => '100%', // As mentioned in UI
        ];

        // Add confidence scores for individual elements
        foreach ($aiDetection as $element => $data) {
            if (is_array($data) && isset($data['confidence'])) {
                $aiAnalysis[$element . '_confidence'] = $data['confidence'];
            }
        }

        return [
            'valid' => false, // Always require manual review for safety
            'confidence' => $confidence,
            'reason' => $isValid
                ? 'Business permit elements detected by custom AI vision model - requires manual review'
                : 'Essential permit elements missing according to custom AI analysis',
            'requires_review' => true,
            'permit_expiry_date' => null,
            'permit_number' => null,
            'ai_analysis' => $aiAnalysis,
        ];
    }

    /**
     * Process results from the legacy verifier (OCR-based).
     */
    protected function processLegacyVerifierResult(array $result, array $metadata): array
    {
        // Convert result to expected format (existing logic)
        $isValid = $result['status'] === 'PENDING_MANUAL_REVIEW';
        $confidence = 0;

        if ($result['ai_confidence'] === 'High') {
            $confidence = 90;
        } elseif ($result['ai_confidence'] === 'Medium') {
            $confidence = 70;
        } else {
            $confidence = 40;
        }

        // Build comprehensive AI analysis
        $aiAnalysis = [
            'document_type' => $result['extracted_data']['document_type'] ?? 'Business Permit',
            'has_official_seals' => $this->extractSealInfo($result),
            'has_signature' => $result['extracted_data']['signature_confidence'] > 0.3,
            'has_registration_number' => !empty($result['extracted_data']['firm_name']),
            'business_name_matches' => $this->checkBusinessNameMatch($result, $metadata),
            'appears_authentic' => $isValid,
            'is_expired' => $this->checkExpiryStatus($result),
            'issuing_authority' => $this->determineIssuingAuthority($result),
            'validity_dates' => $result['extracted_data']['valid_until'] ?? null,
            'recommendation' => $isValid ? 'APPROVE' : 'REJECT',
            'red_flags' => $this->extractRedFlags($result),
            'ai_model_used' => 'mandaluyong_verifier_ocr_based',
        ];

        return [
            'valid' => $isValid,
            'confidence' => $confidence,
            'reason' => $isValid ? 'Valid business permit detected by custom AI' : 'Document failed permit validation',
            'requires_review' => !$isValid,
            'permit_expiry_date' => $result['extracted_data']['valid_until'] ?? null,
            'permit_number' => null,
            'ai_analysis' => $aiAnalysis,
        ];
    }

    /**
     * Extract seal information from verification result.
     */
    protected function extractSealInfo(array $result): bool
    {
        return ($result['extracted_data']['signature_confidence'] ?? 0) > 0.3 ||
               ($result['extracted_data']['seal_confidence'] ?? 0) > 0.3;
    }

    /**
     * Check if business name matches metadata.
     */
    protected function checkBusinessNameMatch(array $result, array $metadata): bool
    {
        $extractedFirm = strtolower($result['extracted_data']['firm_name'] ?? '');
        $expectedFirm = strtolower($metadata['company_name'] ?? '');

        if (empty($expectedFirm)) {
            return true; // No expectation to match against
        }

        // Simple substring match (could be enhanced with fuzzy matching)
        return strpos($extractedFirm, $expectedFirm) !== false ||
               strpos($expectedFirm, $extractedFirm) !== false;
    }

    /**
     * Check if document is expired.
     */
    protected function checkExpiryStatus(array $result): bool
    {
        // This would be determined during the verification process
        // For now, assume not expired if validation passed
        return false;
    }

    /**
     * Determine issuing authority from document type.
     */
    protected function determineIssuingAuthority(array $result): string
    {
        $docType = $result['extracted_data']['document_type'] ?? '';

        if (strpos(strtolower($docType), 'dti') !== false) {
            return 'Department of Trade and Industry (DTI)';
        } elseif (strpos(strtolower($docType), 'barangay') !== false) {
            return 'Barangay Government';
        } elseif (strpos(strtolower($docType), 'mayor') !== false) {
            return 'City Mayor\'s Office';
        } elseif (strpos(strtolower($docType), 'sec') !== false) {
            return 'Securities and Exchange Commission (SEC)';
        }

        return 'Government Authority';
    }

    /**
     * Extract red flags from verification result.
     */
    protected function extractRedFlags(array $result): array
    {
        $redFlags = [];

        if ($result['status'] === 'BLOCKED') {
            $redFlags[] = $result['reason'];
        }

        if (($result['extracted_data']['signature_confidence'] ?? 0) < 0.3) {
            $redFlags[] = 'Missing or unclear signature';
        }

        if (($result['extracted_data']['seal_confidence'] ?? 0) < 0.3) {
            $redFlags[] = 'Missing official seal or stamp';
        }

        if (empty($result['extracted_data']['firm_name'])) {
            $redFlags[] = 'Business name not detected';
        }

        if (empty($result['extracted_data']['owner_name'])) {
            $redFlags[] = 'Owner name not detected';
        }

        return $redFlags;
    }

    /**
     * Check business name match for new AI detector results.
     */
    protected function checkBusinessNameMatchFromNewAI(array $aiDetection, array $metadata): bool
    {
        // New AI detector doesn't extract business names yet, so we can't match
        // Return null to indicate unknown, or true for now since we can't verify
        $expectedFirm = strtolower($metadata['company_name'] ?? '');

        if (empty($expectedFirm)) {
            return true; // No expectation to match against
        }

        // For now, if business details are detected, assume potential match
        // This could be enhanced when the AI detector extracts text
        return $aiDetection['has_business_details']['present'] ?? false;
    }

    /**
     * Extract red flags from new AI detector results.
     */
    protected function extractRedFlagsFromNewAI(array $aiDetection): array
    {
        $redFlags = [];
        $summary = $aiDetection['summary'] ?? [];

        // Check for missing essential elements
        $missingElements = $summary['missing_elements'] ?? [];
        foreach ($missingElements as $element) {
            $redFlags[] = "Missing: " . str_replace('_', ' ', $element);
        }

        // Check confidence levels for detected elements
        $essentialElements = [
            'has_mandaluyong_logo',
            'has_business_permit_title',
            'has_business_details',
            'has_names',
            'has_signatures'
        ];

        foreach ($essentialElements as $element) {
            if (isset($aiDetection[$element]) && $aiDetection[$element]['present']) {
                $confidence = $aiDetection[$element]['confidence'] ?? 0;
                if ($confidence < 0.6) {
                    $redFlags[] = "Low confidence for " . str_replace('_', ' ', $element);
                }
            }
        }

        return $redFlags;
    }

    /**
     * Call the document verifier service using HTTP requests to Flask service.
     * This method MUST succeed with at least one AI service - it will not fall back to non-AI validation.
     */
    protected function callDocumentVerifierService(string $filePath): array
    {
        // Check if file exists
        if (!file_exists($filePath)) {
            Log::error("Document validation failed: File not found at {$filePath}");
            throw new \Exception("File not found: {$filePath}");
        }

        // Get Flask service URL from config
        $baseUrl = config('services.document_verifier.base_url', 'http://localhost:5010');
        $timeout = config('services.document_verifier.timeout', 120);
        $validateUrl = rtrim($baseUrl, '/') . '/validate_document';

        Log::info("Attempting to call Flask AI service at: {$validateUrl} for file: {$filePath}");

        try {
            // First, try to check if Flask service is running
            $healthUrl = rtrim($baseUrl, '/') . '/health';
            try {
                $healthResponse = Http::timeout(5)->get($healthUrl);
                if ($healthResponse->successful()) {
                    Log::info("Flask AI service is healthy");
                } else {
                    Log::warning("Flask AI service health check returned status: " . $healthResponse->status());
                }
            } catch (\Exception $e) {
                Log::warning("Flask AI service health check failed: " . $e->getMessage() . ". Service may not be running at {$baseUrl}");
            }

            // Send file path to Flask service for validation
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->post($validateUrl, [
                    'file_path' => $filePath,
                ]);

            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = $response->body();
                Log::error("Flask AI service returned error status {$statusCode}: {$errorBody}");
                throw new \Exception("Flask AI service returned error status {$statusCode}: " . substr($errorBody, 0, 200));
            }

            $result = $response->json();
            
            if (empty($result)) {
                Log::error("Flask AI service returned empty response");
                throw new \Exception("Flask AI service returned empty response");
            }

            // Check if result contains error
            if (isset($result['error'])) {
                $errorMsg = $result['error'];
                Log::error("Flask AI service returned error: {$errorMsg}");
                throw new \Exception("Flask AI service error: {$errorMsg}");
            }

            // Check if this is from the new AI detector (has ai_detection) or legacy verifier (has status)
            if (isset($result['ai_detection'])) {
                // New AI detector result
                Log::info("✅ Successfully got result from Flask service using new AI detector (vision model)");
                return $this->convertNewAIDetectorResult($result);
            } elseif (isset($result['status'])) {
                // Legacy OCR verifier result
                Log::info("✅ Successfully got result from Flask service using legacy OCR verifier");
                return $result;
            } else {
                $keys = implode(', ', array_keys($result));
                Log::error("Flask AI service returned unexpected result format. Available keys: {$keys}");
                throw new \Exception("Flask AI service returned unexpected result format. Missing 'ai_detection' or 'status' key.");
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $errorMsg = "Cannot connect to Flask AI service at {$baseUrl}. Make sure the Flask service is running on port 5010. Error: " . $e->getMessage();
            Log::error($errorMsg);
            throw new \Exception($errorMsg);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $errorMsg = "Flask AI service request failed: " . $e->getMessage();
            Log::error($errorMsg);
            throw new \Exception($errorMsg);
        } catch (\Exception $e) {
            $errorMsg = "Flask AI service error: " . $e->getMessage();
            Log::error($errorMsg);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Convert new AI detector result format to the expected format.
     */
    protected function convertNewAIDetectorResult(array $result): array
    {
        $aiDetection = $result['ai_detection'] ?? [];
        $summary = $aiDetection['summary'] ?? [];

        // Map new format to expected format
        $status = $summary['is_valid_permit'] ? 'PENDING_MANUAL_REVIEW' : 'BLOCKED';
        $aiConfidence = $this->mapConfidenceToLevel($summary['confidence_score'] ?? 0);

        // Build extracted data
        $extractedData = [
            'document_type' => 'BUSINESS_PERMIT', // Default, could be enhanced
            'firm_name' => null, // New AI doesn't extract this yet
            'owner_name' => null, // New AI doesn't extract this yet
            'issue_date' => null, // New AI doesn't extract this yet
            'valid_until' => null, // New AI doesn't extract this yet
            'signature_confidence' => ($aiDetection['has_signatures']['confidence'] ?? 0) * 100,
            'seal_confidence' => 0.0, // New AI doesn't detect seals specifically
            'ai_detection_results' => $aiDetection, // Store full AI results
        ];

        // Add detected elements info
        $detectedElements = $summary['detected_elements'] ?? [];
        $extractedData['detected_elements'] = $detectedElements;
        $extractedData['missing_elements'] = $summary['missing_elements'] ?? [];

        return [
            'status' => $status,
            'extracted_data' => $extractedData,
            'ai_confidence' => $aiConfidence,
            'reason' => $status === 'BLOCKED' ? 'Missing essential permit elements' : 'Valid permit elements detected',
        ];
    }

    /**
     * Map confidence score to High/Medium/Low level.
     */
    protected function mapConfidenceToLevel(float $confidence): string
    {
        if ($confidence >= 0.8) {
            return 'High';
        } elseif ($confidence >= 0.5) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }


    /**
     * Extract expiry date from validity dates string.
     *
     * @param string $validityDates String containing validity dates
     * @return string|null Expiry date in Y-m-d format or null
     */
    protected function extractExpiryDate(string $validityDates): ?string
    {
        try {
            // Common date patterns in Philippine business permits:
            // "Valid until December 31, 2025"
            // "Valid from: Jan 1, 2025 to: Dec 31, 2025"
            // "Expiry Date: 12/31/2025"
            // "Valid: 2025-01-01 to 2025-12-31"

            // Try to find "until", "to", "expiry", "expires" patterns
            $patterns = [
                '/(?:until|expires?|expiry.*?|to)[:\s]+(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})/i',
                '/(?:until|expires?|expiry)[:\s]+([A-Za-z]+\s+\d{1,2},?\s+\d{4})/i',
                '/to[:\s]+(\d{4}-\d{2}-\d{2})/i',
                '/(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})(?!.*\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4})/', // Last date found
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $validityDates, $matches)) {
                    $dateStr = $matches[1];

                    // Try to parse the date
                    try {
                        $date = new \DateTime($dateStr);
                        return $date->format('Y-m-d');
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to extract expiry date from: ' . $validityDates);
            return null;
        }
    }

    /**
     * Validate resume document.
     * Note: Resume validation currently uses fallback only (custom AI not implemented for resumes yet).
     */
    public function validateResume(string $filePath, array $metadata = []): array
    {
        // Custom AI for resume validation not yet implemented
        return $this->fallbackResumeValidation($filePath);
    }

    /**
     * Fallback resume validation.
     */
    protected function fallbackResumeValidation(string $filePath): array
    {
        return [
            'valid' => false,
            'confidence' => 0,
            'reason' => 'AI validation unavailable. Manual review required.',
            'requires_review' => true,
        ];
    }
}
