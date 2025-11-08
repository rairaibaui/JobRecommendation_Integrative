<?php
// Usage: php scripts/debug_verify.php user@example.com

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the framework kernel so helpers like storage_path() are available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1] ?? null;
if (!$email) {
    echo "Usage: php scripts/debug_verify.php user@example.com\n";
    exit(1);
}

$user = App\Models\User::where('email', $email)->first();
if (!$user) {
    echo "User not found for email: {$email}\n";
    exit(2);
}

$svc = $app->make(App\Services\ResumeVerificationService::class);
$path = $user->resume_file;
if (!$path) {
    echo "User has no resume_file set on model.\n";
    exit(3);
}

echo "Running verification for user {$user->email} on resume: {$path}\n\n";

$result = $svc->verify($path, $user);

// Pretty print the result
echo "=== Verification Result ===\n";
echo "Status: " . ($result['status'] ?? 'N/A') . "\n";
echo "Score: " . ($result['score'] ?? 'N/A') . "\n";
echo "Flags: " . json_encode($result['flags']) . "\n";
echo "Notes: " . ($result['notes'] ?? '') . "\n\n";

echo "--- Extracted Fields ---\n";
foreach ($result['extracted'] as $k => $v) {
    echo ucfirst($k) . ": " . ($v ?? 'N/A') . "\n";
}

echo "\nFull dump:\n";
print_r($result);

return 0;
