<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf as PdfToText;
use ThiagoAlessio\TesseractOCR\TesseractOCR;
use \Imagick;

class AutoPermitVerifierService
{
    /**
     * Extract text from PDF using spatie/pdf-to-text. If that fails or returns
     * very little content, fall back to rendering the first page to an image
     * and running Tesseract OCR on it.
     * Returns the extracted text (uppercased and normalized) and raw_text.
     */
    public function extractTextFromPdf(string $fullPath): array
    {
        $raw = null;

        try {
            $raw = (new PdfToText())->setPdf($fullPath)->text();
        } catch (\Throwable $e) {
            Log::warning('PdfToText failed: ' . $e->getMessage());
            $raw = null;
        }

        // If spatie returned nothing helpful, fallback to Tesseract on a rasterized page
        if (empty(trim((string) $raw))) {
            try {
                // Render first page to PNG via Imagick
                    if (class_exists('\\Imagick')) {
                    $img = new Imagick();
                    $img->setResolution(300, 300);
                    $img->readImage($fullPath . '[0]');
                    $img->setImageFormat('png');
                    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'permit_' . uniqid() . '.png';
                    $img->writeImage($tmp);
                    $img->clear();
                    $img->destroy();

                    // Run tesseract
                    $raw = (new TesseractOCR($tmp))->run();

                    @unlink($tmp);
                } else {
                    Log::warning('Imagick not available for PDF rasterization fallback.');
                }
            } catch (\Throwable $e) {
                Log::warning('Tesseract fallback failed: ' . $e->getMessage());
            }
        }

        $rawText = (string) $raw;
        $normalized = strtoupper(trim(preg_replace('/\s+/', ' ', $rawText)));

        return ['raw_text' => $rawText, 'text' => $normalized];
    }

    /**
     * Verify the extracted text against required keywords and fields.
     * Returns array with status ('PENDING'|'REJECTED'), document_type, fields, review_reason, permit_expiry_date.
     */
    public function verifyText(string $text, string $rawText = null): array
    {
        // Keyword detection
        $docType = 'UNKNOWN';
        if (str_contains($text, "BUSINESS PERMIT") || str_contains($text, "BUSINESS PERMIT")) {
            $docType = 'BUSINESS_PERMIT';
        }
        if (str_contains($text, "MAYOR") && str_contains($text, "PERMIT")) {
            $docType = 'MAYORS_PERMIT';
        }
        if (str_contains($text, 'DTI') || str_contains($text, 'DTI REGISTRATION') || str_contains($text, 'DEPARTMENT OF TRADE')) {
            $docType = 'DTI_REGISTRATION';
        }
        if (str_contains($text, 'BARANGAY CLEARANCE')) {
            $docType = 'BARANGAY_CLEARANCE';
        }
        if (str_contains($text, 'BARANGAY LOCATIONAL CLEARANCE') || str_contains($text, 'BUSINESS LOCATIONAL CLEARANCE')) {
            $docType = 'BARANGAY_LOCATIONAL_CLEARANCE';
        }

        // Field extraction heuristics
        $fields = [];

        // Issued date / Valid until detection using common date patterns
        $dateRegex = '/(\b\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4}\b)|(\b\w{3,9} \d{1,2},? \d{4}\b)/i';
        preg_match_all($dateRegex, $rawText ?? $text, $matches);
        $dates = $matches[0] ?? [];
        if (!empty($dates)) {
            // naive: first date = issued, last date = valid until (if multiple)
            $fields['issued_date'] = $dates[0];
            $fields['valid_until'] = end($dates);
        }

        // Owner name: look for lines that have uppercase words and common labels
        if (preg_match('/(ISSUED TO|TO:|NAME OF OWNER|OWNER|ISSUED TO:)\s*(.+)/i', $rawText ?? $text, $m)) {
            $fields['owner_name'] = trim($m[2]);
        } else {
            // fallback: find lines with 2+ capitalized words
            if (preg_match('/\n([A-Z][A-Z\s]{3,})\n/', $rawText ?? $text, $m2)) {
                $fields['owner_name'] = trim($m2[1]);
            }
        }

        // Signature indication
        $hasSignature = false;
        if (str_contains($text, 'SIGNED BY') || str_contains($text, 'SIGNATURE') || str_contains($text, 'SIGNATORY')) {
            $hasSignature = true;
        }

        // Mandaluyong special rule
        $permitExpiry = null;
        if (str_contains($text, 'MANDALUYONG')) {
            $permitExpiry = '2025-12-31';
            $fields['city'] = 'MANDALUYONG';
        }

        // Validation rules: issued_date, valid_until, owner_name, signature indication
        $missing = [];
        if (empty($fields['issued_date'])) { $missing[] = 'issued_date'; }
        if (empty($fields['valid_until']) && empty($permitExpiry)) { $missing[] = 'valid_until'; }
        if (empty($fields['owner_name'])) { $missing[] = 'owner_name'; }
        if (!$hasSignature) { $missing[] = 'signature'; }

        if ($docType === 'UNKNOWN') {
            return [
                'status' => 'REJECTED',
                'document_type' => $docType,
                'fields' => $fields,
                'review_reason' => 'Document does not contain known permit keywords.',
            ];
        }

        if (!empty($missing)) {
            return [
                'status' => 'REJECTED',
                'document_type' => $docType,
                'fields' => $fields,
                'missing' => $missing,
                'review_reason' => 'Required fields missing: ' . implode(', ', $missing),
            ];
        }

        return [
            'status' => 'PENDING',
            'document_type' => $docType,
            'fields' => $fields,
            'permit_expiry_date' => $permitExpiry,
            'review_reason' => 'Automated checks passed. Forwarded for admin approval.',
        ];
    }

    /**
     * High-level verify: accepts a stored file path (absolute) and returns the verification result
     */
    public function verifyPermit(string $fullPath): array
    {
        $extracted = $this->extractTextFromPdf($fullPath);
        $text = $extracted['text'] ?? '';
        $raw = $extracted['raw_text'] ?? '';

        $result = $this->verifyText($text, $raw);

        // attach raw_text for debugging
        $result['raw_text'] = $raw;
        $result['extracted_text'] = $text;

        return $result;
    }
}
