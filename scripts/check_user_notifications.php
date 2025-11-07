<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'alexaduhac.0706@gmail.com')->first();

if (!$user) {
    echo "User not found\n";
    exit(1);
}

echo "=== User: {$user->email} ===\n\n";
echo "Name: {$user->first_name} {$user->last_name}\n";
echo "Resume Verification Status: {$user->resume_verification_status}\n";
echo "Verification Score: {$user->verification_score}/100\n\n";

echo "=== Latest Notifications ===\n\n";

$notifications = $user->notifications()->latest()->take(5)->get();

if ($notifications->isEmpty()) {
    echo "No notifications found\n";
} else {
    foreach ($notifications as $notification) {
        $read = $notification->read ? '✓ Read' : '○ Unread';
        echo "[{$read}] {$notification->title}\n";
        echo "    {$notification->message}\n";
        echo "    Created: {$notification->created_at->diffForHumans()}\n\n";
    }
}
