<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Notification;

// Find an employer user
$employer = User::where('user_type', 'employer')->first();

if (!$employer) {
    echo "❌ No employer found. Please create an employer account first.\n";
    exit(1);
}

echo "✅ Found employer: {$employer->email} (ID: {$employer->id})\n\n";

// Create a test notification with a link
$notification = Notification::create([
    'user_id' => $employer->id,
    'type' => 'new_application',
    'title' => 'Test: New Application Received',
    'message' => 'This is a test notification with clickable link.',
    'link' => route('employer.applicants'),
    'data' => [
        'test' => true,
        'application_id' => 999,
        'job_title' => 'Test Position',
    ],
]);

echo "✅ Created test notification with ID: {$notification->id}\n";
echo "   Type: {$notification->type}\n";
echo "   Link: {$notification->link}\n";
echo "\n";
echo "📱 Now check the employer's notification dropdown!\n";
echo "   The notification should have a link icon and clicking it should redirect to Applicants page.\n";
