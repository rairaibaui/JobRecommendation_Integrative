<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;

// Update existing test notifications with correct URL
$updated = Notification::where('type', 'new_application')
    ->where('link', 'like', 'http://localhost%')
    ->update(['link' => 'http://127.0.0.1:8000/employer/applicants']);

echo "✅ Updated {$updated} notification(s) with correct URL\n";
echo "   Old URL: http://localhost/employer/applicants\n";
echo "   New URL: http://127.0.0.1:8000/employer/applicants\n";
