<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== APPLICATIONS DEBUG ===\n\n";

// Get all applications
$applications = \App\Models\Application::all();
echo "Total Applications: " . $applications->count() . "\n\n";

foreach ($applications as $app) {
    echo "Application #{$app->id}\n";
    echo "  Job Posting ID: {$app->job_posting_id}\n";
    echo "  User ID: {$app->user_id}\n";
    echo "  Status: {$app->status}\n";
    
    $job = \App\Models\JobPosting::find($app->job_posting_id);
    if ($job) {
        echo "  Job Title: {$job->title}\n";
        echo "  Job Employer ID: {$job->employer_id}\n";
    } else {
        echo "  Job: NOT FOUND!\n";
    }
    echo "\n";
}

echo "\n=== JOB POSTINGS ===\n\n";
$jobs = \App\Models\JobPosting::withCount('applications')->get();
foreach ($jobs as $job) {
    echo "Job #{$job->id}: {$job->title}\n";
    echo "  Employer ID: {$job->employer_id}\n";
    echo "  Applications Count: {$job->applications_count}\n";
    echo "\n";
}

echo "\n=== EMPLOYERS ===\n\n";
$employers = \App\Models\User::where('user_type', 'employer')->get();
foreach ($employers as $emp) {
    echo "Employer #{$emp->id}: {$emp->first_name} {$emp->last_name}\n";
    echo "  Company: " . ($emp->company_name ?? 'N/A') . "\n";
    $jobCount = \App\Models\JobPosting::where('employer_id', $emp->id)->count();
    echo "  Job Postings: {$jobCount}\n";
    echo "\n";
}
