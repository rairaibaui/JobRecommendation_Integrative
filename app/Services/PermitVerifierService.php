<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PermitVerifierService
{
    public function verifyDocument(string $filePath, bool $permissive = false): array
    {
        try {
            // Step 1: OCR
            $ocrText = trim((new TesseractOCR($filePath))->lang('eng')->run());

            // Step 2: Fallback for PDFs
            if (empty($ocrText) && strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'pdf') {
                try {
                    $imagePath = storage_path('app/temp_ocr.png');
                    $imagick = new \Imagick();
                    $imagick->setResolution(300, 300);
                    $imagick->readImage($filePath.'[0]');
                    $imagick->setImageFormat('png');
                    $imagick->writeImage($imagePath);
                    $imagick->clear();
                    $imagick->destroy();

                    $ocrText = trim((new TesseractOCR($imagePath))->lang('eng')->run());
                    @unlink($imagePath);
                } catch (\Throwable $e) {
                    Log::warning('PDF OCR fallback failed: '.$e->getMessage());
                }
            }

            Log::info('OCR extracted text: '.substr($ocrText, 0, 400));

            // Step 3: OpenAI classification with confidence
            $client = \OpenAI::client(env('OPENAI_API_KEY'));

            $prompt = <<<PROMPT
You are a strict document classifier for uploaded images or scans.

Classify the following text extracted from a document. Determine if it is a **Philippine Business Permit** (usually from a city or municipality and includes phrases like "Business Permit", "Mayor’s Permit", "Business Name", "Permit Number", and official seals).

Respond **only** in this JSON format:
{
  "type": "BUSINESS_PERMIT" | "MAYORS_PERMIT" | "BARANGAY_CLEARANCE" | "OTHER",
  "confidence": (a number between 0 and 100)
}

Document text:
---
{$ocrText}
---
PROMPT;

            $response = $client->chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a strict document type classifier.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $aiResult = json_decode($response->choices[0]->message->content ?? '{}', true);
            Log::info('AI classification result:', $aiResult);

            $docType = strtoupper($aiResult['type'] ?? 'UNKNOWN');
            $confidence = $aiResult['confidence'] ?? 0;

            // Step 4: Decision logic
            $status = 'BLOCKED';
            $reviewReason = 'Document does not match expected business permit type.';

            if ($docType === 'BUSINESS_PERMIT' && $confidence >= 70) {
                $status = 'APPROVED';
                $reviewReason = 'Document recognized as a valid business permit.';
            } elseif ($docType !== 'OTHER' && $confidence < 70 && $permissive) {
                $status = 'PENDING';
                $reviewReason = 'AI uncertain — sent for admin review.';
            }

            return [
                'status' => $status,
                'document_type' => $docType,
                'confidence' => $confidence,
                'review_reason' => $reviewReason,
                'ocr_text' => $ocrText,
            ];
        } catch (\Throwable $e) {
            Log::error('PermitVerifierService exception: '.$e->getMessage());

            return [
                'status' => 'PENDING',
                'document_type' => 'UNKNOWN',
                'review_reason' => 'Verification failed — sent for admin review.',
                'ocr_text' => null,
            ];
        }
    }
}
