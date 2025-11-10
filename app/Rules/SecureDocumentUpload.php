<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SecureDocumentUpload implements ValidationRule
{
    private array $allowedMimes;
    private int $maxSize;
    private array $disallowedExtensions;

    public function __construct(array $allowedMimes = [], int $maxSize = 5120, array $disallowedExtensions = [])
    {
        $this->allowedMimes = $allowedMimes ?: ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        $this->maxSize = $maxSize; // in KB
        $this->disallowedExtensions = $disallowedExtensions ?: ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'asp', 'jsp', 'html', 'htm'];
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        // Check file size
        if ($value->getSize() > $this->maxSize * 1024) {
            $fail("The :attribute must not be larger than {$this->maxSize}KB.");
            return;
        }

        // Check MIME type
        if (!in_array($value->getMimeType(), $this->allowedMimes)) {
            $fail('The :attribute must be a PDF or image file (PDF, JPG, PNG).');
            return;
        }

        // Check file extension against disallowed list
        $extension = strtolower($value->getClientOriginalExtension());
        if (in_array($extension, $this->disallowedExtensions)) {
            $fail('The :attribute has an invalid file extension.');
            return;
        }

        // Check for null bytes in filename
        if (str_contains($value->getClientOriginalName(), "\0")) {
            $fail('The :attribute contains invalid characters in the filename.');
            return;
        }

        // Check for suspicious filename patterns
        $filename = $value->getClientOriginalName();
        if (preg_match('/\.\./', $filename) || preg_match('/[<>:"\/\\|?*\x00-\x1f]/', $filename)) {
            $fail('The :attribute contains invalid characters in the filename.');
            return;
        }

        // Verify file content matches MIME type
        $fileContent = file_get_contents($value->getRealPath());

        if ($value->getMimeType() === 'application/pdf') {
            // Check if it's actually a PDF by looking for PDF header
            if (!str_starts_with($fileContent, '%PDF-')) {
                $fail('The :attribute is not a valid PDF file.');
                return;
            }
        } elseif ($this->isImageMime($value->getMimeType())) {
            $imageInfo = @getimagesizefromstring($fileContent);
            if (!$imageInfo) {
                $fail('The :attribute is not a valid image file.');
                return;
            }
        }

        // Check for embedded scripts or malicious content
        if (preg_match('/<script|javascript:|vbscript:|onload=|onerror=|eval\(|document\.|window\.|location\./i', $fileContent)) {
            $fail('The :attribute contains potentially malicious content.');
            return;
        }

        // For PDFs, check for JavaScript content
        if ($value->getMimeType() === 'application/pdf') {
            if (preg_match('/\/JS\s|\/JavaScript\s|\/OpenAction\s/i', $fileContent)) {
                $fail('The :attribute contains potentially malicious JavaScript content.');
                return;
            }
        }
    }

    private function isImageMime(string $mime): bool
    {
        return in_array($mime, ['image/jpeg', 'image/png', 'image/jpg']);
    }
}