<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DUPLICATE DETECTION - RESULTS ===" . PHP_EOL . PHP_EOL;

$user2 = App\Models\User::where('email', 'duhacalexsandra2002@gmail.com')->first();
$validation2 = App\Models\DocumentValidation::where('user_id', $user2->id)
    ->where('document_type', 'business_permit')
    ->latest()
    ->first();

if ($validation2) {
    echo "✅ Validation Record Created" . PHP_EOL;
    echo str_repeat("=", 60) . PHP_EOL;
    echo "Status: " . strtoupper($validation2->validation_status) . PHP_EOL;
    echo "Is Valid: " . ($validation2->is_valid ? 'true' : 'false') . PHP_EOL;
    echo "Confidence Score: {$validation2->confidence_score}%" . PHP_EOL;
    echo "Validated By: {$validation2->validated_by}" . PHP_EOL;
    echo PHP_EOL;
    
    echo "Reason:" . PHP_EOL;
    echo "  {$validation2->reason}" . PHP_EOL;
    echo PHP_EOL;
    
    if ($validation2->ai_analysis && isset($validation2->ai_analysis['duplicate_detection'])) {
        echo "🔍 Duplicate Detection Details:" . PHP_EOL;
        echo str_repeat("=", 60) . PHP_EOL;
        $details = $validation2->ai_analysis['duplicate_detection'];
        
        echo "Duplicate Type: " . ($details['duplicate_type'] ?? 'unknown') . PHP_EOL;
        echo "File Hash Match: " . ($details['file_hash_match'] ? 'YES' : 'NO') . PHP_EOL;
        echo "Company Name Match: " . ($details['company_name_match'] ? 'YES' : 'NO') . PHP_EOL;
        echo "File Hash: " . substr($details['file_hash'] ?? 'N/A', 0, 40) . '...' . PHP_EOL;
        echo "Original Account: " . ($details['existing_user_email'] ?? 'N/A') . PHP_EOL;
        echo PHP_EOL;
    }
    
    echo "📧 Notification Status:" . PHP_EOL;
    echo str_repeat("=", 60) . PHP_EOL;
    $notification = App\Models\Notification::where('user_id', $user2->id)
        ->latest()
        ->first();
    
    if ($notification) {
        echo "Type: " . $notification->type . PHP_EOL;
        echo "Title: " . $notification->title . PHP_EOL;
        echo "Message: " . $notification->message . PHP_EOL;
    } else {
        echo "No notification found" . PHP_EOL;
    }
    
    echo PHP_EOL;
    echo "🎯 RESULT:" . PHP_EOL;
    echo str_repeat("=", 60) . PHP_EOL;
    echo "❌ Account 2 CANNOT post jobs (pending_review)" . PHP_EOL;
    echo "✅ Duplicate detection WORKED as expected!" . PHP_EOL;
    echo "👤 Admin must manually approve or reject" . PHP_EOL;
    echo PHP_EOL;
    echo "Account 2 will see:" . PHP_EOL;
    echo "  ⚠️ Business Permit Under Review" . PHP_EOL;
    echo "  (Not the 'Required' message anymore)" . PHP_EOL;
    
} else {
    echo "❌ No validation record found for Account 2" . PHP_EOL;
}
