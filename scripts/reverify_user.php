<?php
// Usage: php scripts/reverify_user.php user@example.com

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
// Bootstrap the kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1] ?? null;
if (!$email) {
    echo "Usage: php scripts/reverify_user.php user@example.com\n";
    exit(1);
}

/** @var \App\Models\User $user */
$user = \App\Models\User::where('email', $email)->first();
if (!$user) {
    echo "User not found: $email\n";
    exit(2);
}

$svc = app(\App\Services\ResumeVerificationService::class);
$path = $user->resume_file;
if (!$path) {
    echo "User has no resume_file set.\n";
    exit(3);
}

echo "Re-verifying resume for {$user->email} (user id: {$user->id})...\n";
$result = $svc->verify($path, $user);

// Persist back to user
$user->resume_verification_status = $result['status'] ?? $user->resume_verification_status;
$user->verification_score = $result['score'] ?? $user->verification_score;
$user->verification_flags = isset($result['flags']) ? json_encode($result['flags']) : $user->verification_flags;
$user->verification_notes = $result['notes'] ?? $user->verification_notes;
$user->verified_at = $result['verified_at'] ?? $user->verified_at;
$user->save();

echo "Done. New status: {$result['status']}, score: {$result['score']}\n";
if (!empty($result['flags'])) {
    echo "Flags: " . implode(', ', $result['flags']) . "\n";
} else {
    echo "Flags: (none)\n";
}

print_r($result);

return 0;
