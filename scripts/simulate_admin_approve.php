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

// Create a resume verification log showing email mismatch
$log = \App\Models\ResumeVerificationLog::create([
    'user_id' => $user->id,
    'resume_path' => $user->resume_file ?? 'resumes/test.pdf',
    'extracted_full_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
    'extracted_email' => 'different@example.com',
    'extracted_phone' => $user->phone_number,
    'match_name' => true,
    'match_email' => false,
    'match_phone' => true,
    'match_birthday' => true,
    'confidence_name' => 95,
    'confidence_email' => 80,
    'confidence_phone' => 90,
    'confidence_birthday' => 90,
    'overall_status' => 'needs_review',
    'notes' => 'Simulated log: email mismatch',
    'raw_ai_response' => '{}',
]);

echo "Created ResumeVerificationLog id={$log->id}, match_email={$log->match_email}\n";

// Simulate admin approval logic (subset of controller)
$user->resume_verification_status = 'verified';
$user->verification_score = 100;
$user->verified_at = now();
$user->verification_notes = 'Approved by simulated admin';

// compute mismatches from latest log
$mismatchFields = [];
$flags = json_decode($user->verification_flags, true) ?? [];
if (isset($log->match_name) && !$log->match_name) { $mismatchFields[] = 'name'; $flags[] = 'mismatch_name'; }
if (isset($log->match_email) && !$log->match_email) { $mismatchFields[] = 'email'; $flags[] = 'mismatch_email'; }
if (isset($log->match_phone) && !$log->match_phone) { $mismatchFields[] = 'phone'; $flags[] = 'mismatch_phone'; }
if (isset($log->match_birthday) && !$log->match_birthday) { $mismatchFields[] = 'birthday'; $flags[] = 'mismatch_birthday'; }

$user->verification_flags = json_encode(array_values(array_unique($flags)));
$user->save();

echo "After simulated approval: status={$user->resume_verification_status}, flags={$user->verification_flags}\n";

if (! empty($mismatchFields)) {
    $friendly = implode(', ', array_map(function($f){ return ucfirst($f); }, $mismatchFields));
    $message = "Your resume was approved by an administrator, but our review detected differences in the following fields: {$friendly}. Please update your resume so it matches your account profile to avoid application issues.";

    \App\Models\Notification::create([
        'user_id' => $user->id,
        'type' => 'warning',
        'title' => 'Resume approved — please check mismatches',
        'message' => $message,
        'read' => false,
        'data' => ['mismatches' => $mismatchFields],
    ]);

    echo "Notification created about mismatches.\n";

    // mark resume_outdated_at now
    $user->resume_outdated_at = now();
    $user->verification_notes = trim(($user->verification_notes ?? '') . ' Outdated due to mismatched fields ||outdated_due:admin_approval');
    $user->save();
    echo "Marked resume_outdated_at={$user->resume_outdated_at}\n";

    // simulate passage of time: set outdated_at older than grace
    $minutes = config('verification.outdated_grace_minutes', 10);
    $user->resume_outdated_at = now()->subMinutes($minutes + 1);
    $user->save();
    echo "Fast-forwarded resume_outdated_at to {$user->resume_outdated_at}\n";

    // run job immediately
    $job = new \App\Jobs\AutoUnverifyResumeJob($user->id);
    $job->handle();

    $ref = \App\Models\User::find($user->id);
    echo "After AutoUnverify job: status={$ref->resume_verification_status}, resume_outdated_at=" . ($ref->resume_outdated_at ? $ref->resume_outdated_at : 'null') . "\n";
} else {
    echo "No mismatches detected; nothing else done.\n";
}
