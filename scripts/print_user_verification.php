<?php
// One-off script to print verification-related fields for a user by email.
// Usage: php scripts/print_user_verification.php "email@example.com"

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/print_user_verification.php \"email\"\n");
    exit(2);
}

$email = $argv[1];
$base = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
chdir($base);

require $base . 'vendor/autoload.php';
$app = require_once $base . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
// Boot the framework
$status = $kernel->bootstrap();

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

try {
    $userModel = App::make(\App\Models\User::class);
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        echo json_encode(['error' => 'User not found', 'email' => $email]) . PHP_EOL;
        exit(0);
    }

    $out = [
        'id' => $user->id,
        'email' => $user->email,
        'resume_verification_status' => $user->resume_verification_status,
        'verification_notes' => $user->verification_notes,
        'verification_flags' => $user->verification_flags,
        'resume_file' => $user->resume_file,
        'resume_outdated_at' => (string) ($user->resume_outdated_at ?? null),
        'verified_at' => (string) ($user->verified_at ?? null),
    ];

    echo json_encode($out, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Throwable $e) {
    echo json_encode(['error' => 'exception', 'message' => $e->getMessage()]) . PHP_EOL;
    exit(1);
}
