<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::where('user_type', 'employer')->get();

echo "=== All Employer Accounts ===" . PHP_EOL;
echo "Total: " . $users->count() . PHP_EOL . PHP_EOL;

foreach ($users as $user) {
    echo "Email: " . $user->email . PHP_EOL;
    echo "Company: " . ($user->company_name ?? 'N/A') . PHP_EOL;
    echo "Created: " . $user->created_at->format('Y-m-d H:i:s') . PHP_EOL;
    
    // Check validation status
    $validation = App\Models\DocumentValidation::where('user_id', $user->id)
        ->where('document_type', 'business_permit')
        ->latest()
        ->first();
    
    if ($validation) {
        echo "Permit Status: " . $validation->validation_status . PHP_EOL;
        echo "Permit Path: " . ($user->business_permit_path ?? 'N/A') . PHP_EOL;
    } else {
        echo "Permit Status: No permit uploaded" . PHP_EOL;
    }
    
    echo str_repeat('-', 50) . PHP_EOL;
}
