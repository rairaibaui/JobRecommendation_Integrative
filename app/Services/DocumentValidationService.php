<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenAI;

class DocumentValidationService
{
    protected $client;
    protected $model;

    public function __construct()
    {
        $apiKey = config('ai.openai_api_key');

        if (empty($apiKey)) {
            Log::warning('OpenAI API key is not configured for document validation');
            $this->client = null;

            return;
        }

        $this->client = \OpenAI::client($apiKey);
        $this->model = config('ai.vision_model', 'gpt-4o'); // GPT-4 with vision
    }

    /**
     * Validate if uploaded file is a legitimate business permit.
     *
     * @param string $filePath Path to the uploaded file in storage
     * @param array  $metadata Additional context (company name, etc.)
     *
     * @return array Validation result
     */
    public function validateBusinessPermit(string $filePath, array $metadata = []): array
    {
        // Check if AI is configured
        if (!$this->client) {
            return $this->fallbackValidation($filePath, $metadata);
        }

        try {
            // Get file content
            $fileContent = Storage::disk('public')->get($filePath);
            $fullPath = Storage::disk('public')->path($filePath);
            $mimeType = mime_content_type($fullPath);

            // Convert file to base64
            $base64Image = base64_encode($fileContent);

            // Determine image format
            $imageFormat = $this->getImageFormat($mimeType);

            if (!$imageFormat) {
                return [
                    'valid' => false,
                    'confidence' => 0,
                    'reason' => 'Unsupported file format. Only PDF, JPG, JPEG, and PNG are supported.',
                    'ai_analysis' => null,
                    'requires_review' => false,
                ];
            }

            // Build validation prompt
            $prompt = $this->buildValidationPrompt($metadata);

            // Call OpenAI Vision API
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a document verification expert specializing in business permits and official government documents. Your task is to analyze documents and determine if they are legitimate business permits or registrations.',
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:image/{$imageFormat};base64,{$base64Image}",
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3, // Low temperature for consistent analysis
            ]);

            // Parse AI response
            $aiResponse = $response->choices[0]->message->content;

            return $this->parseValidationResponse($aiResponse, $metadata);
        } catch (\Exception $e) {
            Log::error('Document validation error: '.$e->getMessage());

            // If AI fails, use fallback
            return $this->fallbackValidation($filePath, $metadata);
        }
    }

    /**
     * Build prompt for AI document validation.
     */
    protected function buildValidationPrompt(array $metadata): string
    {
        $companyName = $metadata['company_name'] ?? 'Unknown';

        $prompt = "Analyze this document and determine if it is a legitimate business permit or business registration document.\n\n";

        $prompt .= "CONTEXT:\n";
        $prompt .= "- Employer Company Name: {$companyName}\n";
        if (isset($metadata['email'])) {
            $prompt .= "- Email: {$metadata['email']}\n";
        }
        $prompt .= "\n";

        $prompt .= "VALIDATION CRITERIA:\n";
        $prompt .= "1. Is this document a business permit, business registration, DTI registration, SEC certificate, Barangay Clearance, or similar official business document?\n";
        $prompt .= "2. Does it appear to be issued by a legitimate government agency (DTI, SEC, Barangay, City Hall, Mayor's Office)?\n";
        $prompt .= "3. Does it contain official seals, stamps, or government logos (DTI logo, barangay seal, city seal, etc.)?\n";
        $prompt .= "4. Does it have a registration number, permit number, or Business Name Number?\n";
        $prompt .= "5. Does it show validity dates or issuance dates (should be recent, not expired)?\n";
        $prompt .= "6. Does the business name on the document reasonably match or relate to '{$companyName}'?\n";
        $prompt .= "7. Is the document clear and readable (not blurry or obviously fake)?\n";
        $prompt .= "8. Does it appear to be a real document (not a photo, screenshot of something else, or random file)?\n";
        $prompt .= "9. For Philippine business permits: Check for DTI logo, SEC seal, Barangay letterhead, QR codes, reference numbers, or official stamps\n";
        $prompt .= "10. For Philippine documents: Look for signatures of government officials (Barangay Secretary, DTI Secretary, etc.)\n\n";

        $prompt .= "ACCEPTABLE DOCUMENTS (Philippine Context):\n";
        $prompt .= "- DTI Certificate of Business Name Registration (with DTI logo, QR code, Business Name Number)\n";
        $prompt .= "- SEC Certificate of Registration (for corporations)\n";
        $prompt .= "- Barangay Business Clearance/Locational Clearance (with barangay seal)\n";
        $prompt .= "- Mayor's Permit / Business Permit from City Hall\n";
        $prompt .= "- BIR Certificate of Registration\n";
        $prompt .= "- Any combination of the above\n\n";

        $prompt .= "UNACCEPTABLE DOCUMENTS:\n";
        $prompt .= "- Personal photos or selfies\n";
        $prompt .= "- Screenshots of unrelated content\n";
        $prompt .= "- Random PDFs or documents\n";
        $prompt .= "- Blank pages\n";
        $prompt .= "- Receipts or invoices (unless they're official business registration receipts with DTI/SEC stamp)\n";
        $prompt .= "- Obviously fake or altered documents\n";
        $prompt .= "- Expired permits (validity date has passed)\n";
        $prompt .= "- Documents without official seals, logos, or signatures\n\n";

        $prompt .= "RESPONSE FORMAT (JSON only, no additional text):\n";
        $prompt .= "{\n";
        $prompt .= '  "is_business_permit": true/false,'."\n";
        $prompt .= '  "confidence_score": 0-100,'."\n";
        $prompt .= '  "document_type": "description of what this document is",'."\n";
        $prompt .= '  "has_official_seals": true/false,'."\n";
        $prompt .= '  "has_registration_number": true/false,'."\n";
        $prompt .= '  "business_name_matches": true/false/null,'."\n";
        $prompt .= '  "appears_authentic": true/false,'."\n";
        $prompt .= '  "is_expired": true/false/null,'."\n";
        $prompt .= '  "issuing_authority": "name of issuing agency if visible",'."\n";
        $prompt .= '  "validity_dates": "dates if visible",'."\n";
        $prompt .= '  "recommendation": "APPROVE/REJECT/MANUAL_REVIEW",'."\n";
        $prompt .= '  "reason": "brief explanation of decision",'."\n";
        $prompt .= '  "red_flags": ["list", "of", "concerns", "if", "any"]'."\n";
        $prompt .= "}\n\n";

        $prompt .= 'Analyze the uploaded document carefully and respond with ONLY the JSON format above.';

        return $prompt;
    }

    /**
     * Parse AI validation response.
     */
    protected function parseValidationResponse(string $aiResponse, array $metadata): array
    {
        try {
            // Clean response - remove markdown if present
            $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $aiResponse);
            $cleanResponse = trim($cleanResponse);

            $data = json_decode($cleanResponse, true);

            if (!$data || !isset($data['is_business_permit'])) {
                throw new \Exception('Invalid AI response format');
            }

            $isValid = $data['is_business_permit']
                       && $data['appears_authentic']
                       && !($data['is_expired'] ?? false);

            $confidenceScore = $data['confidence_score'] ?? 0;
            $recommendation = strtoupper($data['recommendation'] ?? 'MANUAL_REVIEW');

            // Determine final result
            $requiresReview = false;
            $finalValid = false;

            if ($recommendation === 'APPROVE' && $confidenceScore >= 80 && $isValid) {
                $finalValid = true;
                $requiresReview = false;
            } elseif ($recommendation === 'REJECT' || $confidenceScore < 50 || !$isValid) {
                $finalValid = false;
                $requiresReview = false;
            } else {
                // MANUAL_REVIEW or uncertain cases
                $finalValid = false;
                $requiresReview = true;
            }

            return [
                'valid' => $finalValid,
                'confidence' => $confidenceScore,
                'reason' => $data['reason'] ?? 'Unable to verify document',
                'requires_review' => $requiresReview,
                'ai_analysis' => [
                    'document_type' => $data['document_type'] ?? 'Unknown',
                    'has_official_seals' => $data['has_official_seals'] ?? false,
                    'has_registration_number' => $data['has_registration_number'] ?? false,
                    'business_name_matches' => $data['business_name_matches'] ?? null,
                    'appears_authentic' => $data['appears_authentic'] ?? false,
                    'is_expired' => $data['is_expired'] ?? null,
                    'issuing_authority' => $data['issuing_authority'] ?? null,
                    'validity_dates' => $data['validity_dates'] ?? null,
                    'recommendation' => $recommendation,
                    'red_flags' => $data['red_flags'] ?? [],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to parse document validation response: '.$e->getMessage());
            Log::debug('AI Response: '.$aiResponse);

            // Return uncertain result requiring manual review
            return [
                'valid' => false,
                'confidence' => 0,
                'reason' => 'Unable to validate document automatically. Manual review required.',
                'requires_review' => true,
                'ai_analysis' => null,
            ];
        }
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
     * Get image format from MIME type.
     */
    protected function getImageFormat(string $mimeType): ?string
    {
        $formats = [
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpeg',
            'image/png' => 'png',
            'application/pdf' => 'pdf', // Note: OpenAI might need special handling for PDFs
        ];

        return $formats[$mimeType] ?? null;
    }

    /**
     * Validate resume document.
     */
    public function validateResume(string $filePath, array $metadata = []): array
    {
        if (!$this->client) {
            return $this->fallbackResumeValidation($filePath);
        }

        try {
            $fileContent = Storage::disk('public')->get($filePath);
            $fullPath = Storage::disk('public')->path($filePath);
            $mimeType = mime_content_type($fullPath);
            $base64Image = base64_encode($fileContent);
            $imageFormat = $this->getImageFormat($mimeType);

            if (!$imageFormat) {
                return [
                    'valid' => false,
                    'confidence' => 0,
                    'reason' => 'Unsupported file format for resume.',
                    'requires_review' => false,
                ];
            }

            $userName = $metadata['user_name'] ?? 'Unknown';

            $prompt = "Analyze this document and determine if it is a legitimate resume or CV.\n\n";
            $prompt .= "Expected Resume Elements:\n";
            $prompt .= "- Contact information (name, email, phone)\n";
            $prompt .= "- Work experience or employment history\n";
            $prompt .= "- Education background\n";
            $prompt .= "- Skills section\n\n";
            $prompt .= "Unacceptable Documents:\n";
            $prompt .= "- Random photos or images\n";
            $prompt .= "- Blank documents\n";
            $prompt .= "- Screenshots of unrelated content\n\n";
            $prompt .= 'Respond with JSON: {"is_resume": true/false, "confidence": 0-100, "reason": "explanation"}';

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => "data:image/{$imageFormat};base64,{$base64Image}"]],
                        ],
                    ],
                ],
                'max_tokens' => 500,
                'temperature' => 0.3,
            ]);

            $aiResponse = $response->choices[0]->message->content;
            $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $aiResponse);
            $data = json_decode(trim($cleanResponse), true);

            return [
                'valid' => $data['is_resume'] ?? false,
                'confidence' => $data['confidence'] ?? 0,
                'reason' => $data['reason'] ?? 'Unable to validate resume',
                'requires_review' => !($data['is_resume'] ?? false) && ($data['confidence'] ?? 0) < 80,
            ];
        } catch (\Exception $e) {
            Log::error('Resume validation error: '.$e->getMessage());

            return $this->fallbackResumeValidation($filePath);
        }
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
