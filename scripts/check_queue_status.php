<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Queue Jobs Status ===" . PHP_EOL . PHP_EOL;

// Check pending jobs in database queue
$pendingJobs = DB::table('jobs')->count();
echo "Pending Jobs in Queue: " . $pendingJobs . PHP_EOL;

if ($pendingJobs > 0) {
    echo PHP_EOL . "Jobs waiting to be processed:" . PHP_EOL;
    $jobs = DB::table('jobs')->orderBy('id', 'desc')->limit(5)->get();
    
    foreach ($jobs as $job) {
        $payload = json_decode($job->payload, true);
        $command = $payload['displayName'] ?? 'Unknown';
        
        echo "- Job ID: {$job->id}" . PHP_EOL;
        echo "  Type: {$command}" . PHP_EOL;
        echo "  Queue: {$job->queue}" . PHP_EOL;
        echo "  Attempts: {$job->attempts}" . PHP_EOL;
        echo "  Available at: " . date('Y-m-d H:i:s', $job->available_at) . PHP_EOL;
        echo PHP_EOL;
    }
    
    echo "⚠️  QUEUE WORKER IS NOT RUNNING!" . PHP_EOL;
    echo PHP_EOL;
    echo "To process these jobs, run:" . PHP_EOL;
    echo "  php artisan queue:work --tries=3" . PHP_EOL;
} else {
    echo "✅ No pending jobs" . PHP_EOL;
}

echo PHP_EOL;
echo "=== Account 2 File Upload Status ===" . PHP_EOL;

$user2 = App\Models\User::where('email', 'duhacalexsandra2002@gmail.com')->first();

if ($user2) {
    echo "User ID: {$user2->id}" . PHP_EOL;
    echo "Company: {$user2->company_name}" . PHP_EOL;
    echo "Business Permit Path: " . ($user2->business_permit_path ?? 'NULL') . PHP_EOL;
    
    if ($user2->business_permit_path) {
        $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($user2->business_permit_path);
        echo "File Exists: " . ($exists ? 'YES' : 'NO') . PHP_EOL;
        
        if ($exists) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($user2->business_permit_path);
            $hash = hash_file('sha256', $path);
            echo "File Hash: {$hash}" . PHP_EOL;
            echo PHP_EOL;
            echo "This hash should match Account 1's permit hash." . PHP_EOL;
        }
    } else {
        echo PHP_EOL;
        echo "❌ No file path stored in database!" . PHP_EOL;
        echo "This means the file upload didn't save to the user record." . PHP_EOL;
    }
}
