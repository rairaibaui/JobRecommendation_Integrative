<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class RecommendationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->user_type === 'job_seeker') {
            // Skills-based matching for job seekers
            $jobs = $this->getSkillBasedRecommendations($user);
        } else {
            // Default behavior for non-logged in users or employers
            $jobs = JobPosting::active()
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
                        'match_score' => 0,
                        'matching_skills' => collect(),
                    ];
                })
                ->toArray();
        }

        // Determine which jobs are bookmarked by current user (by title)
        $bookmarkedTitles = [];
        if (auth()->check()) {
            $bookmarkedTitles = auth()->user()->bookmarks()->pluck('title')->toArray();
        }

        // Pass to the Blade view
        return view('recommendation', ['jobs' => $jobs, 'bookmarkedTitles' => $bookmarkedTitles]);
    }

    /**
     * Get skill-based job recommendations for a job seeker
     */
    private function getSkillBasedRecommendations($user): array
    {
        // Parse user's skills (assuming comma-separated string)
        $userSkills = $this->parseSkills($user->skills ?? '');

        if ($userSkills->isEmpty()) {
            // If user has no skills, return recent jobs
            return JobPosting::active()
                ->with('employer')
                ->orderByDesc('created_at')
                ->limit(20)
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
                        'match_score' => 0,
                        'matching_skills' => collect(),
                    ];
                })
                ->toArray();
        }

        // Get jobs with skill matching
        $recommendedJobs = JobPosting::active()
            ->with('employer')
            ->get()
            ->map(function($job) use ($userSkills) {
                // Parse job skills
                $jobSkills = $this->parseSkills($job->skills ?? '');

                // Find matching skills
                $matchingSkills = $jobSkills->intersect($userSkills);

                // Calculate match score (percentage of job skills that user has)
                $matchScore = $jobSkills->count() > 0
                    ? ($matchingSkills->count() / $jobSkills->count()) * 100
                    : 0;

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
                    'match_score' => round($matchScore, 1),
                    'matching_skills' => $matchingSkills,
                    'job_skills' => $jobSkills,
                ];
            })
            ->filter(function($job) {
                // Only include jobs with at least some skill match
                return $job['match_score'] > 0;
            })
            ->sortByDesc('match_score')
            ->take(20) // Top 20 recommendations
            ->values()
            ->toArray();

        return $recommendedJobs;
    }

    /**
     * Parse skills string into a collection of normalized skills
     */
    private function parseSkills($skillsInput): Collection
    {
        // Handle array input (from JSON)
        if (is_array($skillsInput)) {
            return collect($skillsInput)
                ->map(function($skill) {
                    return trim(strtolower($skill));
                })
                ->filter(function($skill) {
                    return !empty($skill);
                })
                ->unique();
        }

        // Handle string input (comma-separated)
        if (empty($skillsInput) || !is_string($skillsInput)) {
            return collect();
        }

        return collect(explode(',', $skillsInput))
            ->map(function($skill) {
                return trim(strtolower($skill));
            })
            ->filter(function($skill) {
                return !empty($skill);
            })
            ->unique();
    }
}
