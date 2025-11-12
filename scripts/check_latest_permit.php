<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PermitVerifierService
{
    /**
     * Verify a document file (PDF or image).
     */
    public function verifyDocument(string $filePath, bool $allowFallbackToPending = true): array
    {
        $ocrText = null;
        $status = 'BLOCKED';
        $type = 'UNKNOWN';
        $reason = null;

        try {
            // --- Detect and OCR ---
            $ocrText = (new TesseractOCR($filePath))->run();
        } catch (\Throwable $e) {
            // OCR failed outright
            Log::error('Tesseract OCR error: '.$e->getMessage(), ['file' => $filePath]);
        }

        // --- Fallback: PDF → PNG rasterization ---
        if (empty(trim($ocrText)) && str_ends_with(strtolower($filePath), '.pdf')) {
            try {
                $img = new \Imagick();
                $img->setResolution(300, 300);
                $img->readImage($filePath.'[0]'); // first page only
                $img->setImageFormat('png');

                $tmpPath = storage_path('app/tmp_ocr_'.uniqid().'.png');
                $img->writeImage($tmpPath);
                $img->clear();
                $img->destroy();

                $ocrText = (new TesseractOCR($tmpPath))->run();

                @unlink($tmpPath); // clean up
            } catch (\Throwable $e) {
                Log::error('Imagick fallback OCR failed: '.$e->getMessage(), ['file' => $filePath]);
            }
        }

        // --- Text classification ---
        $result = $this->verifyText($ocrText);

        // --- Downgrade BLOCKED to PENDING if allowed ---
        if ($allowFallbackToPending && $result['status'] === 'BLOCKED') {
            $result['status'] = 'PENDING';
            $result['review_reason'] = 'Low-confidence OCR — sent for manual review.';
        }

        // --- OCR debug logging ---
        $statusLog = $result['status'] ?? 'BLOCKED';
        if (empty($ocrText) || in_array($statusLog, ['BLOCKED', 'PENDING'])) {
            Log::info('OCR_DEBUG', [
                'file' => $filePath,
                'status' => $statusLog,
                'type' => $result['document_type'] ?? 'UNKNOWN',
                'reason' => $result['review_reason'] ?? null,
                'text' => substr(trim($ocrText ?? ''), 0, 400), // first 400 chars only
            ]);
        }

        return $result;
    }

    /**
     * Analyze OCR text and classify document type.
     */
    public function verifyText(?string $ocrText): array
    {
        $text = strtoupper(trim(preg_replace('/\s+/', ' ', $ocrText ?? '')));

        if (str_contains($text, 'MAYOR') && str_contains($text, 'PERMIT')) {
            return ['status' => 'PENDING', 'document_type' => 'MAYORS_PERMIT'];
        }

        if (
            str_contains($text, 'BUSINESS LOCATIONAL CLEARANCE')
            || str_contains($text, 'BARANGAY LOCATION')
            || str_contains($text, 'BARANGAY LOCATIONAL CLEARANCE')
        ) {
            return ['status' => 'PENDING', 'document_type' => 'BARANGAY_LOCATIONAL_CLEARANCE'];
        }

        if (str_contains($text, 'BUSINESS PERMIT')) {
            return ['status' => 'PENDING', 'document_type' => 'BUSINESS_PERMIT'];
        }

        // --- Default: unknown / blocked ---
        return [
            'status' => 'BLOCKED',
            'document_type' => 'UNKNOWN',
            'review_reason' => 'Uploaded file does not appear to be a valid business permit or clearance.',
        ];
    }
}
