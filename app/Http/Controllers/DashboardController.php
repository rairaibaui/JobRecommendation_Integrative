<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\JobPosting;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // If job seeker, show only skill-matched jobs in dashboard
        if ($user && ($user->user_type ?? null) === 'job_seeker') {
            $userSkills = $this->parseSkills($user->skills ?? '');

            if ($userSkills->isNotEmpty()) {
                $jobs = JobPosting::active()
                    ->with('employer')
                    ->get()
                    ->map(function($job) use ($userSkills) {
                        $jobSkills = $this->parseSkills($job->skills ?? '');
                        $matchingSkills = $jobSkills->intersect($userSkills);
                        $matchScore = $jobSkills->count() > 0
                            ? ($matchingSkills->count() / $jobSkills->count()) * 100
                            : 0;

                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'company' => $job->company_name ?? 'Company',
                            'location' => $job->location ?? 'Mandaluyong City',
                            'type' => $job->type ?? 'Full-Time',
                            'salary' => $job->salary ?? 'Negotiable',
                            'description' => $job->description ?? '',
                            'skills' => $job->skills ?? [],
                            'apply_url' => '#',
                            'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                            'employer_email' => $job->employer->email ?? '',
                            'employer_phone' => $job->employer->phone_number ?? '',
                            'posted_date' => $job->created_at->format('M d, Y'),
                            'match_score' => round($matchScore, 1),
                            'matching_skills' => $matchingSkills,
                            'job_skills' => $jobSkills,
                        ];
                    })
                    ->filter(function($job){ return $job['match_score'] > 0; })
                    ->sortByDesc('match_score')
                    ->take(10)
                    ->values()
                    ->toArray();
            } else {
                // No skills in profile: return recent jobs (fallback), without match info
                $jobs = JobPosting::active()
                    ->with('employer')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                    ->map(function($job) {
                        return [
                            'id' => $job->id,
                            'title' => $job->title,
                            'company' => $job->company_name ?? 'Company',
                            'location' => $job->location ?? 'Mandaluyong City',
                            'type' => $job->type ?? 'Full-Time',
                            'salary' => $job->salary ?? 'Negotiable',
                            'description' => $job->description ?? '',
                            'skills' => $job->skills ?? [],
                            'apply_url' => '#',
                            'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                            'employer_email' => $job->employer->email ?? '',
                            'employer_phone' => $job->employer->phone_number ?? '',
                            'posted_date' => $job->created_at->format('M d, Y'),
                            'match_score' => 0,
                            'matching_skills' => collect(),
                            'job_skills' => collect(),
                        ];
                    })
                    ->toArray();
            }
        } else {
            // Non job seekers: recent jobs
            $jobs = JobPosting::active()
                ->with('employer')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company_name ?? 'Company',
                        'location' => $job->location ?? 'Mandaluyong City',
                        'type' => $job->type ?? 'Full-Time',
                        'salary' => $job->salary ?? 'Negotiable',
                        'description' => $job->description ?? '',
                        'skills' => $job->skills ?? [],
                        'apply_url' => '#',
                        'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                        'employer_email' => $job->employer->email ?? '',
                        'employer_phone' => $job->employer->phone_number ?? '',
                        'posted_date' => $job->created_at->format('M d, Y'),
                        'match_score' => 0,
                        'matching_skills' => collect(),
                        'job_skills' => collect(),
                    ];
                })
                ->toArray();
        }

        // determine which jobs are bookmarked by current user
        $bookmarkedTitles = [];
        if (Auth::check()) {
            $u = \App\Models\User::find(Auth::id());
            if ($u) {
                $bookmarkedTitles = $u->bookmarks()->pluck('title')->toArray();
            }
        }

        return view('dashboard', compact('jobs', 'bookmarkedTitles'));
    }

    /**
     * Parse skills from JSON array or comma-separated string into a normalized collection
     */
    private function parseSkills($skillsInput)
    {
        if (is_array($skillsInput)) {
            return collect($skillsInput)
                ->map(function($skill){ return trim(strtolower($skill)); })
                ->filter(fn($s) => !empty($s))
                ->unique();
        }
        if (empty($skillsInput) || !is_string($skillsInput)) {
            return collect();
        }
        return collect(explode(',', $skillsInput))
            ->map(function($skill){ return trim(strtolower($skill)); })
            ->filter(fn($s) => !empty($s))
            ->unique();
    }

    public function recommendation()
    {
        $jobs = [
            [
                'title' => 'Cashier',
                'location' => 'Mandaluyong City',
                'type' => 'Full-time',
                'salary' => 'Php 645/day',
                'description' => 'Handle cash transactions, provide customer service, and maintain a clean and organized checkout area.',
                'skills' => ['Customer Service', 'Cash Handling', 'Basic Math'],
            ],
            [
                'title' => 'Sales Associate',
                'location' => 'Mandaluyong City',
                'type' => 'Full-time',
                'salary' => 'Php 645/day',
                'description' => 'Assist customers, process sales transactions, and maintain store appearance.',
                'skills' => ['Customer Service', 'Cash Handling', 'Sales'],
            ],
        ];

        return view('recommendation', compact('jobs'));
    }

    public function bookmarks()
    {
        $bookmarkedJobs = [];

       return view('bookmarks', [
    'bookmarkedJobs' => collect($bookmarkedJobs)
]);
    }

    public function settings()
    {
        /** @var \App\Models\User $user */
        $user = \App\Models\User::find(Auth::id());
        if (($user->user_type ?? null) === 'employer') {
            return view('employer.settings', compact('user'));
        }
        return view('settings', compact('user'));
    }

    public function changePassword()
    {
        return view('change-password');
    }

    public function clearBookmarks(Request $request)
    {
        // Logic to clear bookmarks - assuming bookmarks are stored in session or database
        // For now, we'll clear session bookmarks
        $request->session()->forget('bookmarkedJobs');

        return redirect()->route('settings')->with('success', 'All bookmarks cleared successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

    /** @var \App\Models\User $user */
    $user = \App\Models\User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('settings')->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('settings')->with('success', 'Password changed successfully!');
    }
}
