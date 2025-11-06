<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find the latest pending review validation
$validation = App\Models\DocumentValidation::where('document_type', 'business_permit')
    ->where('validation_status', 'pending_review')
    ->latest()
    ->first();

if (!$validation) {
    echo "No pending validations found." . PHP_EOL;
    exit(0);
}

echo "=== Approving Validation ===" . PHP_EOL;
echo "User: " . $validation->user->email . PHP_EOL;
echo "Company: " . $validation->user->company_name . PHP_EOL;
echo "Current Status: " . $validation->validation_status . PHP_EOL;
echo PHP_EOL;

// Approve the validation
$validation->validation_status = 'approved';
$validation->is_valid = true;
$validation->validated_by = 'admin';
$validation->validated_at = now();
$validation->reason = 'Approved - verification was incorrectly reset due to profile update (not permit change)';
$validation->save();

// Create success notification
App\Models\Notification::create([
    'user_id' => $validation->user_id,
    'type' => 'success',
    'title' => 'Business Permit Re-Approved',
    'message' => 'Your business permit has been re-approved. You can now post job listings.',
    'read' => false,
]);

echo "✅ Validation approved successfully!" . PHP_EOL;
echo "New Status: " . $validation->validation_status . PHP_EOL;
echo "Employer can now post jobs." . PHP_EOL;
