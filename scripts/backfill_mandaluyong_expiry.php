<?php

// One-off script to backfill permit_expiry_date = 2025-12-31
// for records that contain 'mandaluyong' in OCR/raw/ai_analysis and have null expiry.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the application so Eloquent and config are available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EmployerDocument;
use App\Models\DocumentValidation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$targetDate = Carbon::create(2025, 12, 31)->toDateString();

echo "Starting backfill for Mandaluyong expiry (setting to $targetDate)\n";

// EmployerDocument: search in ocr_text or raw_text fields
try {
    $edQuery = EmployerDocument::whereNull('permit_expiry_date')
        ->where(function($q) {
            $q->whereRaw('LOWER(COALESCE(ocr_text, "")) LIKE ?', ['%mandaluyong%'])
              ->orWhereRaw('LOWER(COALESCE(raw_text, "")) LIKE ?', ['%mandaluyong%']);
        });

    $edCount = $edQuery->count();

    if ($edCount > 0) {
        echo "Found $edCount employer_documents to update...\n";
        foreach ($edQuery->cursor() as $doc) {
            $doc->permit_expiry_date = $targetDate;
            $doc->save();
            echo " - Updated employer_document id={$doc->id}\n";
        }
    } else {
        echo "No employer_documents to update.\n";
    }
} catch (\Throwable $e) {
    echo "Skipping employer_documents backfill: ". $e->getMessage() ."\n";
}

// DocumentValidation: search in ai_analysis, ocr_text or raw_text or reason
try {
    $dvQuery = DocumentValidation::whereNull('permit_expiry_date')
        ->where(function($q) {
            $q->whereRaw('LOWER(COALESCE(ai_analysis, "")) LIKE ?', ['%mandaluyong%'])
              ->orWhereRaw('LOWER(COALESCE(ocr_text, "")) LIKE ?', ['%mandaluyong%'])
              ->orWhereRaw('LOWER(COALESCE(raw_text, "")) LIKE ?', ['%mandaluyong%'])
              ->orWhereRaw('LOWER(COALESCE(reason, "")) LIKE ?', ['%mandaluyong%']);
        });

    $dvCount = $dvQuery->count();

    if ($dvCount > 0) {
        echo "Found $dvCount document_validations to update...\n";
        foreach ($dvQuery->cursor() as $v) {
            $v->permit_expiry_date = $targetDate;
            $v->save();
            echo " - Updated document_validation id={$v->id}\n";
        }
    } else {
        echo "No document_validations to update.\n";
    }
} catch (\Throwable $e) {
    echo "Skipping document_validations backfill: ". $e->getMessage() ."\n";
}

echo "Backfill complete.\n";
