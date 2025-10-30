<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SIMULATING EMPLOYER APPLICANTS PAGE ===\n\n";

// Test with employer ID 9 (who has 2 job postings and 1 application)
$employerId = 9;
$employer = \App\Models\User::find($employerId);

if (!$employer) {
    echo "Employer not found!\n";
    exit;
}

echo "Logged in as: {$employer->first_name} {$employer->last_name} (ID: {$employer->id})\n";
echo "Company: " . ($employer->company_name ?? 'N/A') . "\n\n";

// Get employer's job postings with their applications (same query as controller)
$jobPostingsQuery = \App\Models\JobPosting::where('employer_id', $employer->id)
    ->withCount('applications')
    ->with(['applications' => function($query) {
        $query->orderByDesc('created_at');
    }])
    ->orderByDesc('created_at');

$jobPostings = $jobPostingsQuery->get();

echo "Job Postings Found: {$jobPostings->count()}\n\n";

foreach ($jobPostings as $job) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Job #{$job->id}: {$job->title}\n";
    echo "Applications Count: {$job->applications_count}\n";
    
    if ($job->applications->count() > 0) {
        echo "\nApplications:\n";
        foreach ($job->applications as $app) {
            $snapshot = $app->resume_snapshot ?? [];
            $name = ($snapshot['first_name'] ?? 'N/A') . ' ' . ($snapshot['last_name'] ?? '');
            echo "  • Application #{$app->id}: {$name}\n";
            echo "    Status: {$app->status}\n";
            echo "    Applied: {$app->created_at}\n";
        }
    } else {
        echo "\n  No applications yet.\n";
    }
    echo "\n";
}

// Calculate stats
$allApplications = \App\Models\Application::whereIn('job_posting_id', 
    \App\Models\JobPosting::where('employer_id', $employer->id)->pluck('id')
);

$stats = [
    'total' => $allApplications->count(),
    'pending' => (clone $allApplications)->where('status', 'pending')->count(),
    'reviewing' => (clone $allApplications)->where('status', 'reviewing')->count(),
    'for_interview' => (clone $allApplications)->where('status', 'for_interview')->count(),
    'interviewed' => (clone $allApplications)->where('status', 'interviewed')->count(),
    'accepted' => (clone $allApplications)->where('status', 'accepted')->count(),
    'rejected' => (clone $allApplications)->where('status', 'rejected')->count(),
];

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "STATISTICS:\n";
foreach ($stats as $key => $value) {
    echo "  {$key}: {$value}\n";
}
