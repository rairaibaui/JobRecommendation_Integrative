<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;
use App\Services\ResumeVerificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResumeVerified;

echo "=== Resume Verification Issue Fix ===\n\n";

// Find users with resume files but have "missing_resume" flag
$users = User::where('user_type', 'job_seeker')
    ->whereNotNull('resume_file')
    ->get()
    ->filter(function($user) {
        $flags = json_decode($user->verification_flags ?? '[]', true) ?: [];
        return in_array('missing_resume', $flags) || in_array('Missing Resume', $flags);
    });

echo "Found " . $users->count() . " users with resume files but 'missing_resume' flag\n\n";

if ($users->isEmpty()) {
    echo "No issues found. All good!\n";
    exit(0);
}

$verificationService = new ResumeVerificationService();

foreach ($users as $user) {
    echo "Processing: {$user->email}\n";
    echo "  Resume file: {$user->resume_file}\n";
    
    // Check if file actually exists
    if (!Storage::disk('public')->exists($user->resume_file)) {
        echo "  ❌ File does NOT exist on disk. Cleaning up database record...\n";
        $user->resume_file = null;
        $user->resume_verification_status = 'pending';
        $user->verification_flags = null;
        $user->verification_score = 0;
        $user->verification_notes = null;
        $user->save();
        echo "  ✅ Database cleaned. User needs to re-upload resume.\n";
    } else {
        echo "  ✅ File exists on disk. Re-running verification...\n";
        
        // Re-run verification
        $result = $verificationService->verify($user->resume_file, $user);
        
        // Update user record
        $user->resume_verification_status = $result['status'];
        $user->verification_flags = json_encode($result['flags']);
        $user->verification_score = $result['score'];
        $user->verification_notes = $result['notes'];
        $user->verified_at = $result['verified_at'];
        $user->save();
        
        echo "  ✅ Verification updated:\n";
        echo "     Status: {$result['status']}\n";
        echo "     Score: {$result['score']}/100\n";
        echo "     Flags: " . implode(', ', $result['flags']) . "\n";
        
        // Send notification and email if verified
        if ($result['status'] === 'verified') {
            try {
                // Create notification
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'success',
                    'title' => 'Resume Verified ✓',
                    'message' => 'Great news! Your resume has been successfully verified and approved. You can now apply for jobs with confidence.',
                    'read' => false,
                    'data' => [
                        'verification_status' => 'verified',
                        'verification_score' => $result['score'],
                        'verified_at' => now()->toDateTimeString(),
                    ],
                ]);
                echo "  📧 Notification created\n";
                
                // Send email
                Mail::to($user->email)->send(new ResumeVerified($user, $result['status'], $result['score']));
                echo "  📧 Email sent to {$user->email}\n";
            } catch (\Exception $e) {
                echo "  ⚠️ Failed to send notification/email: {$e->getMessage()}\n";
            }
        }
    }
    
    echo "\n" . str_repeat('-', 60) . "\n\n";
}

echo "✅ Fix complete!\n";
