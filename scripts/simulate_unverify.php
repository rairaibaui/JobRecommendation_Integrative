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

// Ensure the user is verified and set resume_outdated_at to older than grace
$user->resume_verification_status = 'verified';
$user->resume_outdated_at = now()->subMinutes(config('verification.outdated_grace_minutes') + 1);
$user->save();

echo 'Set resume_outdated_at to: ' . $user->resume_outdated_at->toDateTimeString() . PHP_EOL;

$job = new \App\Jobs\AutoUnverifyResumeJob($user->id);
$job->handle();

$ref = \App\Models\User::find($user->id);
echo 'After job: status=' . ($ref->resume_verification_status ?? 'null') . ', resume_outdated_at=' . ($ref->resume_outdated_at ? $ref->resume_outdated_at : 'null') . PHP_EOL;
