<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;

class RecommendationController extends Controller
{
    public function index()
    {
        // Get active job postings from database with employer information
        $jobs = JobPosting::active()
            ->with('employer') // Load employer relationship
            ->orderByDesc('created_at')
            ->get()
            ->map(function($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'location' => $job->location ?? 'Mandaluyong',
                    'type' => $job->type ?? 'Full-time',
                    'salary' => $job->salary ?? 'Negotiable',
                    'description' => $job->description ?? '',
                    'skills' => $job->skills ?? [],
                    'company' => $job->company_name ?? '',
                    // Employer contact information
                    'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                    'employer_email' => $job->employer->email ?? '',
                    'employer_phone' => $job->employer->phone_number ?? '',
                    'posted_date' => $job->created_at->format('M d, Y'),
                ];
            })
            ->toArray();

        // Determine which jobs are bookmarked by current user (by title)
        $bookmarkedTitles = [];
        if (auth()->check()) {
            $bookmarkedTitles = auth()->user()->bookmarks()->pluck('title')->toArray();
        }

        // Pass to the Blade view
        return view('recommendation', ['jobs' => $jobs, 'bookmarkedTitles' => $bookmarkedTitles]);
    }
}
