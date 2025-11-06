<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$validation = App\Models\DocumentValidation::first();

if ($validation) {
    $validation->validation_status = 'approved';
    $validation->confidence_score = 95;
    $validation->is_valid = true;
    $validation->reason = 'Business permit validated successfully. Manually approved by administrator.';
    $validation->save();
    
    echo "✅ Employer account approved!\n";
    echo "User ID: {$validation->user_id}\n";
    echo "Status: {$validation->validation_status}\n";
    echo "Confidence: {$validation->confidence_score}%\n";
} else {
    echo "❌ No validation record found.\n";
}
