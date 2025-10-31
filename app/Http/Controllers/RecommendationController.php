<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index()
    {
        // All available jobs (for the "Job Recommendation" section)
        $allJobs = JobPosting::active()
            ->with('employer')
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
                    'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                    'employer_email' => $job->employer->email ?? '',
                    'employer_phone' => $job->employer->phone_number ?? '',
                    'posted_date' => $job->created_at->format('M d, Y'),
                    // No matching metadata needed for the full list
                    'match_score' => 0,
                    'matching_skills' => collect(),
                ];
            })
            ->toArray();

        // Determine which jobs are bookmarked by current user (by title)
        $bookmarkedTitles = [];
        if (Auth::check()) {
            /** @var \App\Models\User $u */
            $u = \App\Models\User::find(Auth::id());
            if ($u) {
                $bookmarkedTitles = $u->bookmarks()->pluck('title')->toArray();
            }
        }

        // Pass to the Blade view (Top section removed; only all jobs shown)
        return view('recommendation', [
            'allJobs' => $allJobs,
            'bookmarkedTitles' => $bookmarkedTitles,
        ]);
    }
}
