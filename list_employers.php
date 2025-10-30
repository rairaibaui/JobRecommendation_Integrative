<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL EMPLOYERS ===\n\n";

$employers = \App\Models\User::where('user_type', 'employer')->get();

foreach ($employers as $emp) {
    echo "ID: {$emp->id}\n";
    echo "Name: {$emp->first_name} {$emp->last_name}\n";
    echo "Email: {$emp->email}\n";
    echo "Company: " . ($emp->company_name ?? 'N/A') . "\n";
    echo "Phone: " . ($emp->phone_number ?? 'N/A') . "\n";
    
    $jobCount = \App\Models\JobPosting::where('employer_id', $emp->id)->count();
    echo "Job Postings: {$jobCount}\n";
    
    if ($jobCount > 0) {
        $jobs = \App\Models\JobPosting::where('employer_id', $emp->id)->get();
        foreach ($jobs as $job) {
            $appCount = \App\Models\Application::where('job_posting_id', $job->id)->count();
            echo "  • {$job->title} ({$appCount} applications)\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}
