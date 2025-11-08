<?php
// Usage: php scripts/dump_user.php user@example.com
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1] ?? null;
if (!$email) { echo "Usage: php scripts/dump_user.php user@example.com\n"; exit(1); }
$user = \App\Models\User::where('email', $email)->first();
if (!$user) { echo "User not found: $email\n"; exit(2); }

$data = [
    'id' => $user->id,
    'email' => $user->email,
    'first_name' => $user->first_name,
    'last_name' => $user->last_name,
    'resume_file' => $user->resume_file,
    'resume_verification_status' => $user->resume_verification_status,
    'verification_flags' => $user->verification_flags,
    'verification_score' => $user->verification_score,
    'verification_notes' => $user->verification_notes,
    'email_verified_at' => $user->email_verified_at,
];
print_r($data);
