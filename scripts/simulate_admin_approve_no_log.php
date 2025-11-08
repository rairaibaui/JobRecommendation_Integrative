<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'duhacalexsandra2002@gmail.com';
$user = \App\Models\User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(1);
}

echo "Found user id={$user->id}, status={$user->resume_verification_status}\n";

// Simulate admin approval with detected email mismatch
$user->resume_verification_status = 'verified';
$user->verification_score = 100;
$user->verified_at = now();
$user->verification_notes = 'Approved by simulated admin';

$flags = json_decode($user->verification_flags, true) ?? [];
$flags[] = 'mismatch_email';
$user->verification_flags = json_encode(array_values(array_unique($flags)));
$user->save();

echo "After simulated approval: status={$user->resume_verification_status}, flags={$user->verification_flags}\n";

// Create notification about mismatches
$friendly = 'Email';
$message = "Your resume was approved by an administrator, but our review detected differences in the following fields: {$friendly}. Please update your resume so it matches your account profile to avoid application issues.";
\App\Models\Notification::create([
    'user_id' => $user->id,
    'type' => 'warning',
    'title' => 'Resume approved — please check mismatches',
    'message' => $message,
    'read' => false,
    'data' => ['mismatches' => ['email']],
]);

echo "Notification created about mismatches.\n";

// Mark resume_outdated_at now then fast-forward to past
$user->resume_outdated_at = now();
$user->verification_notes = trim(($user->verification_notes ?? '') . ' Outdated due to mismatched fields ||outdated_due:admin_approval');
$user->save();

echo "Marked resume_outdated_at={$user->resume_outdated_at}\n";

$minutes = config('verification.outdated_grace_minutes', 10);
$user->resume_outdated_at = now()->subMinutes($minutes + 1);
$user->save();

echo "Fast-forwarded resume_outdated_at to {$user->resume_outdated_at}\n";

// run job immediately
$job = new \App\Jobs\AutoUnverifyResumeJob($user->id);
$job->handle();

$ref = \App\Models\User::find($user->id);
echo "After AutoUnverify job: status={$ref->resume_verification_status}, resume_outdated_at=" . ($ref->resume_outdated_at ? $ref->resume_outdated_at : 'null') . "\n";
