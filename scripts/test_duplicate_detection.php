<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Duplicate Detection Test ===" . PHP_EOL . PHP_EOL;

// Get both accounts
$account1 = App\Models\User::where('email', 'alexsandra.duhac2002@gmail.com')->first();
$account2 = App\Models\User::where('email', 'duhacalexsandra2002@gmail.com')->first();

if (!$account1 || !$account2) {
    echo "Error: Could not find both accounts" . PHP_EOL;
    exit(1);
}

echo "Account 1: {$account1->email} - {$account1->company_name}" . PHP_EOL;
echo "Account 2: {$account2->email} - {$account2->company_name}" . PHP_EOL;
echo PHP_EOL;

// Get Account 1's permit
$validation1 = App\Models\DocumentValidation::where('user_id', $account1->id)
    ->where('document_type', 'business_permit')
    ->first();

if (!$validation1) {
    echo "Account 1 has no business permit uploaded." . PHP_EOL;
    exit(1);
}

echo "Account 1 Permit:" . PHP_EOL;
echo "- Status: {$validation1->validation_status}" . PHP_EOL;
echo "- File Path: {$validation1->file_path}" . PHP_EOL;
echo "- File Hash: " . ($validation1->file_hash ?? 'NULL (needs update)') . PHP_EOL;

// Calculate and update hash if missing
if (!$validation1->file_hash && \Illuminate\Support\Facades\Storage::disk('public')->exists($validation1->file_path)) {
    $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($validation1->file_path);
    $fileHash = hash_file('sha256', $filePath);
    $validation1->file_hash = $fileHash;
    $validation1->save();
    echo "- File Hash (calculated): {$fileHash}" . PHP_EOL;
}

echo PHP_EOL;

// Check if Account 2 has uploaded the same permit
$validation2 = App\Models\DocumentValidation::where('user_id', $account2->id)
    ->where('document_type', 'business_permit')
    ->first();

if ($validation2) {
    echo "Account 2 Permit:" . PHP_EOL;
    echo "- Status: {$validation2->validation_status}" . PHP_EOL;
    echo "- File Path: {$validation2->file_path}" . PHP_EOL;
    echo "- Reason: {$validation2->reason}" . PHP_EOL;
    
    if ($validation2->file_hash && \Illuminate\Support\Facades\Storage::disk('public')->exists($validation2->file_path)) {
        echo "- File Hash: {$validation2->file_hash}" . PHP_EOL;
        
        if ($validation1->file_hash === $validation2->file_hash) {
            echo "✅ DUPLICATE DETECTED: Same file hash!" . PHP_EOL;
        }
    }
    
    if ($validation2->ai_analysis && isset($validation2->ai_analysis['duplicate_detection'])) {
        echo PHP_EOL . "Duplicate Detection Info:" . PHP_EOL;
        print_r($validation2->ai_analysis['duplicate_detection']);
    }
} else {
    echo "Account 2 has not uploaded a permit yet." . PHP_EOL;
    echo PHP_EOL;
    echo "When Account 2 uploads the same permit:" . PHP_EOL;
    echo "✅ System will detect:" . PHP_EOL;
    echo "   - Same file hash (exact duplicate)" . PHP_EOL;
    echo "   - Same company name: '{$account1->company_name}'" . PHP_EOL;
    echo "   - Validation will be flagged as 'pending_review'" . PHP_EOL;
    echo "   - Admin must manually approve/reject" . PHP_EOL;
}
