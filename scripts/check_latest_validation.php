<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DocumentValidation;
use App\Models\User;

$email = $argv[1] ?? 'duhacalexsandra2002@gmail.com';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found: {$email}" . PHP_EOL;
    exit(1);
}

echo "=== LATEST VALIDATION FOR {$email} ===" . PHP_EOL . PHP_EOL;

$validation = DocumentValidation::where('user_id', $user->id)
    ->where('document_type', 'business_permit')
    ->latest()
    ->first();

if (!$validation) {
    echo "No validation found." . PHP_EOL;
    exit(0);
}

echo "Validation ID: {$validation->id}" . PHP_EOL;
echo "Status: " . strtoupper($validation->validation_status) . PHP_EOL;
echo "Validated By: {$validation->validated_by}" . PHP_EOL;
echo "Is Valid: " . ($validation->is_valid ? 'YES' : 'NO') . PHP_EOL;
echo "Confidence: {$validation->confidence_score}%" . PHP_EOL;
echo "File Path: {$validation->file_path}" . PHP_EOL;
echo "File Hash: " . ($validation->file_hash ? substr($validation->file_hash, 0, 40) . '...' : 'NULL') . PHP_EOL;
echo "Permit Number: " . ($validation->permit_number ?? 'NULL') . PHP_EOL;
echo "Expiry Date: " . ($validation->permit_expiry_date ?? 'NULL') . PHP_EOL;
echo "Created: {$validation->created_at}" . PHP_EOL;
echo PHP_EOL;

echo "Reason:" . PHP_EOL;
echo "  " . $validation->reason . PHP_EOL;
echo PHP_EOL;

if ($validation->ai_analysis) {
    echo "AI Analysis:" . PHP_EOL;
    echo json_encode($validation->ai_analysis, JSON_PRETTY_PRINT) . PHP_EOL;
}
