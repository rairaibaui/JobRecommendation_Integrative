<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;

// Get the latest notification to check its structure
$notification = Notification::orderByDesc('created_at')->first();

if (!$notification) {
    echo "❌ No notifications found.\n";
    exit(1);
}

echo "Latest Notification Structure:\n";
echo "================================\n";
echo "ID: {$notification->id}\n";
echo "Type: {$notification->type}\n";
echo "Title: {$notification->title}\n";
echo "Message: {$notification->message}\n";
echo "Link: " . ($notification->link ?? 'NULL') . "\n";
echo "Data: " . json_encode($notification->data, JSON_PRETTY_PRINT) . "\n";
echo "\n";
echo "JSON Output (what API returns):\n";
echo json_encode($notification->toArray(), JSON_PRETTY_PRINT) . "\n";
