<?php

namespace App\Services;

use App\Models\User;
use App\Models\JobPosting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI;

class AIRecommendationService
{
    protected $client;
    protected $model;
    protected $temperature;
    protected $maxTokens;

    public function __construct()
    {
        $apiKey = config('ai.openai_api_key');
        
        if (empty($apiKey)) {
            Log::warning('OpenAI API key is not configured');
            $this->client = null;
            return;
        }

        $this->client = OpenAI::client($apiKey);
        $this->model = config('ai.model', 'gpt-3.5-turbo');
        $this->temperature = config('ai.temperature', 0.7);
        $this->maxTokens = config('ai.max_tokens', 1500);
    }

    /**
     * Get AI-powered job recommendations for a user
     *
     * @param User $user
     * @param Collection $jobs
     * @return array
     */
    public function getRecommendations(User $user, Collection $jobs): array
    {
        // Check if AI is configured
        if (!$this->client) {
            return $this->fallbackRecommendations($user, $jobs);
        }

        // Check cache
        $cacheKey = "ai_recommendations_user_{$user->id}";
        $cacheDuration = config('ai.cache_duration', 60);

        if ($cacheDuration > 0 && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $recommendations = $this->generateAIRecommendations($user, $jobs);

            // Cache the results
            if ($cacheDuration > 0) {
                Cache::put($cacheKey, $recommendations, now()->addMinutes($cacheDuration));
            }

            return $recommendations;
        } catch (\Exception $e) {
            Log::error('AI Recommendation Error: ' . $e->getMessage());
            return $this->fallbackRecommendations($user, $jobs);
        }
    }

    /**
     * Generate AI-powered recommendations using OpenAI
     *
     * @param User $user
     * @param Collection $jobs
     * @return array
     */
    protected function generateAIRecommendations(User $user, Collection $jobs): array
    {
        // Prepare user profile for AI
        $userProfile = $this->buildUserProfile($user);

        // Limit jobs to analyze
        $maxJobs = config('ai.recommendations.max_jobs_to_analyze', 50);
        $jobsToAnalyze = $jobs->take($maxJobs);

        // Prepare jobs data for AI
        $jobsData = $this->buildJobsData($jobsToAnalyze);

        // Create the prompt
        $prompt = $this->buildPrompt($userProfile, $jobsData);

        // Call OpenAI API
        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert career advisor and job matching AI. Your task is to analyze user profiles and job postings to provide highly accurate job recommendations with match scores and detailed explanations.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
        ]);

        // Parse AI response
        $aiResponse = $response->choices[0]->message->content;
        
        return $this->parseAIResponse($aiResponse, $jobsToAnalyze);
    }

    /**
     * Build user profile for AI analysis
     *
     * @param User $user
     * @return array
     */
    protected function buildUserProfile(User $user): array
    {
        return [
            'name' => $user->first_name . ' ' . $user->last_name,
            'job_title' => $user->job_title ?? 'Not specified',
            'skills' => $this->parseSkills($user->skills ?? ''),
            'education_level' => $user->education_level ?? 'Not specified',
            'education' => $user->education ?? [],
            'experiences' => $user->experiences ?? [],
            'years_of_experience' => $user->years_of_experience ?? 0,
            'location' => $user->location ?? 'Not specified',
            'summary' => $user->summary ?? 'No summary provided',
            'availability' => $user->availability ?? 'Not specified',
        ];
    }

    /**
     * Build jobs data for AI analysis
     *
     * @param Collection $jobs
     * @return array
     */
    protected function buildJobsData(Collection $jobs): array
    {
        return $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company_name ?? 'Company',
                'location' => $job->location ?? 'Not specified',
                'type' => $job->type ?? 'Full-Time',
                'salary' => $job->salary ?? 'Negotiable',
                'description' => substr($job->description ?? '', 0, 500),
                'skills' => $this->parseSkills($job->skills ?? ''),
            ];
        })->toArray();
    }

    /**
     * Build the prompt for AI
     *
     * @param array $userProfile
     * @param array $jobsData
     * @return string
     */
    protected function buildPrompt(array $userProfile, array $jobsData): string
    {
        $maxRecommendations = config('ai.recommendations.max_recommendations', 10);
        $enableExplanations = config('ai.recommendations.enable_explanations', true);

        $prompt = "Analyze the following user profile and job postings to provide the top {$maxRecommendations} job recommendations.\n\n";
        
        $prompt .= "USER PROFILE:\n";
        $prompt .= "Name: {$userProfile['name']}\n";
        $prompt .= "Current/Desired Job Title: {$userProfile['job_title']}\n";
        $prompt .= "Skills: " . implode(', ', $userProfile['skills']) . "\n";
        $prompt .= "Education Level: {$userProfile['education_level']}\n";
        $prompt .= "Years of Experience: {$userProfile['years_of_experience']}\n";
        $prompt .= "Location: {$userProfile['location']}\n";
        $prompt .= "Summary: {$userProfile['summary']}\n\n";

        $prompt .= "AVAILABLE JOBS:\n";
        foreach ($jobsData as $index => $job) {
            $prompt .= "Job ID {$job['id']}:\n";
            $prompt .= "  Title: {$job['title']}\n";
            $prompt .= "  Company: {$job['company']}\n";
            $prompt .= "  Location: {$job['location']}\n";
            $prompt .= "  Type: {$job['type']}\n";
            $prompt .= "  Required Skills: " . implode(', ', $job['skills']) . "\n";
            $prompt .= "  Description: {$job['description']}\n\n";
        }

        $prompt .= "TASK:\n";
        $prompt .= "Provide the top {$maxRecommendations} job recommendations in JSON format.\n";
        $prompt .= "For each recommendation, include:\n";
        $prompt .= "- job_id: The ID of the job\n";
        $prompt .= "- match_score: A score from 0-100 indicating how well the job matches the user\n";
        if ($enableExplanations) {
            $prompt .= "- explanation: A brief explanation (2-3 sentences) of why this job is recommended\n";
        }
        $prompt .= "- matching_skills: Array of skills that match between user and job\n";
        $prompt .= "- career_growth: Brief note on career growth potential\n\n";
        
        $prompt .= "Sort recommendations by match_score (highest first).\n";
        $prompt .= "Only recommend jobs with match_score >= " . config('ai.recommendations.min_match_score', 30) . ".\n\n";
        
        $prompt .= "Return ONLY valid JSON, no additional text:\n";
        $prompt .= '{"recommendations": [{"job_id": 1, "match_score": 95, "explanation": "...", "matching_skills": [...], "career_growth": "..."}]}';

        return $prompt;
    }

    /**
     * Parse AI response and merge with job data
     *
     * @param string $aiResponse
     * @param Collection $jobs
     * @return array
     */
    protected function parseAIResponse(string $aiResponse, Collection $jobs): array
    {
        try {
            // Clean up response - remove markdown code blocks if present
            $cleanResponse = preg_replace('/```json\s*|\s*```/', '', $aiResponse);
            $cleanResponse = trim($cleanResponse);

            $aiData = json_decode($cleanResponse, true);

            if (!isset($aiData['recommendations'])) {
                throw new \Exception('Invalid AI response format');
            }

            $recommendations = [];
            foreach ($aiData['recommendations'] as $rec) {
                $job = $jobs->firstWhere('id', $rec['job_id']);
                if ($job) {
                    $jobSkills = $this->parseSkills($job->skills ?? '');
                    $matchingSkills = collect($rec['matching_skills'] ?? []);
                    
                    $recommendations[] = [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company_name ?? 'Company',
                        'location' => $job->location ?? 'Mandaluyong City',
                        'type' => $job->type ?? 'Full-Time',
                        'salary' => $job->salary ?? 'Negotiable',
                        'description' => $job->description ?? '',
                        'skills' => $job->skills ?? [],
                        'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                        'employer_email' => $job->employer->email ?? '',
                        'employer_phone' => $job->employer->phone_number ?? '',
                        'posted_date' => $job->created_at->format('M d, Y'),
                        'match_score' => $rec['match_score'] ?? 0,
                        'ai_explanation' => $rec['explanation'] ?? '',
                        'matching_skills' => $matchingSkills,
                        'job_skills' => $jobSkills,
                        'career_growth' => $rec['career_growth'] ?? '',
                    ];
                }
            }

            return $recommendations;
        } catch (\Exception $e) {
            Log::error('Failed to parse AI response: ' . $e->getMessage());
            Log::debug('AI Response: ' . $aiResponse);
            throw $e;
        }
    }

    /**
     * Fallback to basic skill matching if AI is not available
     *
     * @param User $user
     * @param Collection $jobs
     * @return array
     */
    protected function fallbackRecommendations(User $user, Collection $jobs): array
    {
        $userSkills = $this->parseSkills($user->skills ?? '');
        
        if ($userSkills->isEmpty()) {
            // Return recent jobs without matching
            return $jobs->take(10)->map(function($job) {
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
                    'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                    'employer_email' => $job->employer->email ?? '',
                    'employer_phone' => $job->employer->phone_number ?? '',
                    'posted_date' => $job->created_at->format('M d, Y'),
                    'match_score' => 0,
                    'ai_explanation' => 'Complete your profile to get personalized recommendations',
                    'matching_skills' => collect(),
                    'job_skills' => $jobSkills,
                    'career_growth' => '',
                ];
            })->toArray();
        }

        // Basic skill-based matching
        $recommendations = $jobs->map(function($job) use ($userSkills) {
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
                'employer_name' => $job->employer ? ($job->employer->first_name . ' ' . $job->employer->last_name) : '',
                'employer_email' => $job->employer->email ?? '',
                'employer_phone' => $job->employer->phone_number ?? '',
                'posted_date' => $job->created_at->format('M d, Y'),
                'match_score' => round($matchScore, 1),
                'ai_explanation' => $matchScore > 0 
                    ? "Matched {$matchingSkills->count()} of {$jobSkills->count()} required skills"
                    : 'No skill match found',
                'matching_skills' => $matchingSkills,
                'job_skills' => $jobSkills,
                'career_growth' => '',
            ];
        })
        ->filter(fn($job) => $job['match_score'] > 0)
        ->sortByDesc('match_score')
        ->take(10)
        ->values()
        ->toArray();

        return $recommendations;
    }

    /**
     * Parse skills from various formats
     *
     * @param mixed $skillsInput
     * @return Collection
     */
    protected function parseSkills($skillsInput): Collection
    {
        if (is_array($skillsInput)) {
            return collect($skillsInput)
                ->map(fn($skill) => trim(strtolower($skill)))
                ->filter(fn($s) => !empty($s))
                ->unique();
        }
        
        if (empty($skillsInput) || !is_string($skillsInput)) {
            return collect();
        }
        
        return collect(explode(',', $skillsInput))
            ->map(fn($skill) => trim(strtolower($skill)))
            ->filter(fn($s) => !empty($s))
            ->unique();
    }

    /**
     * Get AI-powered career insights for a user
     *
     * @param User $user
     * @return string
     */
    public function getCareerInsights(User $user): string
    {
        if (!$this->client || !config('ai.features.career_insights')) {
            return 'AI career insights are not available at the moment.';
        }

        try {
            $userProfile = $this->buildUserProfile($user);
            
            $prompt = "Based on this professional profile, provide 3-4 brief career insights and recommendations:\n\n";
            $prompt .= "Job Title: {$userProfile['job_title']}\n";
            $prompt .= "Skills: " . implode(', ', $userProfile['skills']) . "\n";
            $prompt .= "Experience: {$userProfile['years_of_experience']} years\n";
            $prompt .= "Education: {$userProfile['education_level']}\n";
            $prompt .= "Summary: {$userProfile['summary']}\n\n";
            $prompt .= "Provide actionable insights about:\n";
            $prompt .= "1. Skills to develop\n2. Career paths to consider\n3. Market trends relevant to their profile\n";

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a career advisor providing brief, actionable insights.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            Log::error('AI Career Insights Error: ' . $e->getMessage());
            return 'Unable to generate career insights at this time.';
        }
    }

    /**
     * Clear cached recommendations for a user
     *
     * @param int $userId
     * @return void
     */
    public function clearCache(int $userId): void
    {
        $cacheKey = "ai_recommendations_user_{$userId}";
        Cache::forget($cacheKey);
    }
}
