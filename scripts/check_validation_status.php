<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$validation = App\Models\DocumentValidation::where('document_type', 'business_permit')
    ->latest()
    ->first();

if ($validation) {
    echo "=== Latest Business Permit Validation ===" . PHP_EOL;
    echo "Status: " . $validation->validation_status . PHP_EOL;
    echo "Is Valid: " . ($validation->is_valid ? 'true' : 'false') . PHP_EOL;
    echo "Confidence: " . $validation->confidence_score . "%" . PHP_EOL;
    echo "Validated By: " . $validation->validated_by . PHP_EOL;
    echo "Reason: " . $validation->reason . PHP_EOL;
    echo "User Email: " . $validation->user->email . PHP_EOL;
    echo "Company: " . ($validation->user->company_name ?? 'N/A') . PHP_EOL;
    echo "Created: " . $validation->created_at . PHP_EOL;
    echo "Expiry Date: " . ($validation->permit_expiry_date ?? 'NULL') . PHP_EOL;
    echo PHP_EOL;
    
    // Check if this was triggered by profile update
    $recentAuditLogs = App\Models\AuditLog::where('user_id', $validation->user_id)
        ->where('event', 'employer_profile_updated')
        ->latest()
        ->take(3)
        ->get();
        
    if ($recentAuditLogs->count() > 0) {
        echo "=== Recent Profile Updates ===" . PHP_EOL;
        foreach ($recentAuditLogs as $log) {
            echo "- " . $log->created_at . ": " . $log->title . PHP_EOL;
            if ($log->data) {
                $data = json_decode($log->data, true);
                echo "  Changed fields: " . implode(', ', $data['changed_fields'] ?? []) . PHP_EOL;
            }
        }
    }
} else {
    echo "No validation records found." . PHP_EOL;
}
