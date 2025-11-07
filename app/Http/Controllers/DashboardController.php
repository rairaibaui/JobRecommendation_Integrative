<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\JobPosting;
use App\Services\AIRecommendationService;

class DashboardController extends Controller
{
    protected $aiService;

    public function __construct(AIRecommendationService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $user = Auth::user();
        // Compute profile completeness for job seekers
        $profileMissing = [];
        $profileCompletePercent = 100;
        $needsProfileReminder = false;

        if ($user && ($user->user_type ?? null) === 'job_seeker') {
            // Determine missing fields critical for better matches and applications
            $skillsCollection = $this->parseSkills($user->skills ?? '');
            if ($skillsCollection->isEmpty()) {
                $profileMissing[] = 'Skills';
            }
            if (empty($user->summary)) {
                $profileMissing[] = 'Professional Summary';
            }
            if (empty($user->location)) {
                $profileMissing[] = 'Location';
            }
            if (empty($user->phone_number)) {
                $profileMissing[] = 'Phone Number';
            }
            // Check education_level field instead of education array
            if (empty($user->education_level)) {
                $profileMissing[] = 'Education';
            }
            // Check years_of_experience field instead of experiences array
            if (!isset($user->years_of_experience) || $user->years_of_experience === null || $user->years_of_experience === '') {
                $profileMissing[] = 'Work Experience';
            }
            if (empty($user->resume_file)) {
                $profileMissing[] = 'Resume';
            }
            if (empty($user->profile_picture)) {
                $profileMissing[] = 'Profile Picture';
            }

            $totalFields = 8; // Keep in sync with checks above
            $missingCount = count($profileMissing);
            $profileCompletePercent = max(0, min(100, round((($totalFields - $missingCount) / $totalFields) * 100)));
            $needsProfileReminder = $missingCount > 0;
        }

        // If job seeker, show AI-powered recommendations
        if ($user && ($user->user_type ?? null) === 'job_seeker') {
            $userSkills = $this->parseSkills($user->skills ?? '');

            // Get all active jobs for AI analysis
            $allJobs = JobPosting::active()->with('employer')->get();

            // Use AI recommendations if enabled, otherwise fallback to basic matching
            if (config('ai.features.job_matching', false) && config('ai.openai_api_key')) {
                try {
                    $jobs = $this->aiService->getRecommendations($user, $allJobs);
                } catch (\Exception $e) {
                    Log::error('AI recommendation failed, using fallback: ' . $e->getMessage());
                    $jobs = $this->basicSkillMatching($user, $allJobs, $userSkills);
                }
            } else {
                // Use basic skill matching as fallback
                $jobs = $this->basicSkillMatching($user, $allJobs, $userSkills);
            }
        } else {
            // Non job seekers: recent jobs
            $jobs = JobPosting::active()
                ->with('employer')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function($job) {
                    $jobSkills = $this->parseSkills($job->skills ?? '');
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
                        'job_skills' => $jobSkills,
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

        return view('dashboard', compact('jobs', 'bookmarkedTitles', 'profileMissing', 'profileCompletePercent', 'needsProfileReminder'));
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
            // Include validation for displaying verification badge
            $validation = \App\Models\DocumentValidation::where('user_id', $user->id)
                ->where('document_type', 'business_permit')
                ->first();
            return view('employer.settings', compact('user', 'validation'));
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

    /**
     * Basic skill-based matching fallback
     */
    private function basicSkillMatching($user, $jobs, $userSkills)
    {
        if ($userSkills->isNotEmpty()) {
            return $jobs
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
                        'ai_explanation' => '',
                        'career_growth' => '',
                    ];
                })
                ->filter(function($job) { return $job['match_score'] > 0; })
                ->sortByDesc('match_score')
                ->take(10)
                ->values()
                ->toArray();
        } else {
            // No skills: return recent jobs
            return $jobs
                ->sortByDesc('created_at')
                ->take(10)
                ->map(function($job) {
                    $jobSkills = $this->parseSkills($job->skills ?? '');
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
                        'job_skills' => $jobSkills,
                        'ai_explanation' => '',
                        'career_growth' => '',
                    ];
                })
                ->toArray();
        }
    }
}
