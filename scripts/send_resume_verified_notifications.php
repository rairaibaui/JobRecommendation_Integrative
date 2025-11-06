<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResumeVerified;

echo "=== Send Resume Verified Notifications ===\n\n";

// Find verified users who might not have received notification/email
$verifiedUsers = User::where('user_type', 'job_seeker')
    ->where('resume_verification_status', 'verified')
    ->whereNotNull('resume_file')
    ->get();

echo "Found " . $verifiedUsers->count() . " verified job seekers\n\n";

foreach ($verifiedUsers as $user) {
    echo "Processing: {$user->email}\n";
    echo "  Name: {$user->first_name} {$user->last_name}\n";
    echo "  Score: {$user->verification_score}/100\n";
    
    try {
        // Create notification
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Resume Verified ✓',
            'message' => 'Great news! Your resume has been successfully verified and approved. You can now apply for jobs with confidence.',
            'read' => false,
            'data' => [
                'verification_status' => 'verified',
                'verification_score' => $user->verification_score,
                'verified_at' => $user->verified_at ?? now()->toDateTimeString(),
            ],
        ]);
        echo "  ✅ Notification created (ID: {$notification->id})\n";
        
        // Send email
        Mail::to($user->email)->send(new ResumeVerified($user, 'verified', $user->verification_score ?? 100));
        echo "  📧 Email sent to {$user->email}\n";
        
        echo "  ✅ Success!\n";
    } catch (\Exception $e) {
        echo "  ❌ Error: {$e->getMessage()}\n";
    }
    
    echo "\n" . str_repeat('-', 60) . "\n\n";
}

echo "✅ All notifications and emails sent!\n";
