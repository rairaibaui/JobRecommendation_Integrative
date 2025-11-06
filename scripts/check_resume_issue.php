<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Find job seekers with resume files but "needs_review" status
$users = User::where('user_type', 'job_seeker')
    ->whereNotNull('resume_file')
    ->where('resume_verification_status', 'needs_review')
    ->get();

echo "Found " . $users->count() . " users with resume files but 'needs_review' status\n\n";

foreach ($users as $user) {
    echo "User: {$user->email}\n";
    echo "Resume file: {$user->resume_file}\n";
    echo "Status: {$user->resume_verification_status}\n";
    echo "Flags: {$user->verification_flags}\n";
    echo "Score: {$user->verification_score}\n";
    
    $flags = json_decode($user->verification_flags ?? '[]', true) ?: [];
    if (in_array('missing_resume', $flags) || in_array('Missing Resume', $flags)) {
        echo "❌ ISSUE: 'missing_resume' flag is set even though resume_file exists!\n";
    }
    echo "\n" . str_repeat('-', 60) . "\n\n";
}
