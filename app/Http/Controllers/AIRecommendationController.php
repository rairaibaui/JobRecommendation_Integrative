<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AIRecommendationService;
use App\Models\JobPosting;
use App\Models\User;

class AIRecommendationController extends Controller
{
    protected $aiService;

    public function __construct(AIRecommendationService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get AI-powered job recommendations
     */
    public function getRecommendations(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'success' => false,
                'message' => 'Only job seekers can get recommendations'
            ], 403);
        }

        try {
            $jobs = JobPosting::active()->with('employer')->get();
            $recommendations = $this->aiService->getRecommendations($user, $jobs);

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
                'count' => count($recommendations),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get career insights for the authenticated user
     */
    public function getCareerInsights(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'success' => false,
                'message' => 'Only job seekers can get career insights'
            ], 403);
        }

        try {
            $insights = $this->aiService->getCareerInsights($user);

            return response()->json([
                'success' => true,
                'insights' => $insights,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate career insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh AI recommendations (clear cache)
     */
    public function refreshRecommendations(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $this->aiService->clearCache($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Recommendations cache cleared. Refresh the page to see new recommendations.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display AI recommendations page
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || $user->user_type !== 'job_seeker') {
            return redirect()->route('dashboard')->with('error', 'Only job seekers can access AI recommendations');
        }

        $jobs = JobPosting::active()->with('employer')->get();
        $recommendations = $this->aiService->getRecommendations($user, $jobs);

        // Get career insights if enabled
        $careerInsights = null;
        if (config('ai.features.career_insights')) {
            try {
                $careerInsights = $this->aiService->getCareerInsights($user);
            } catch (\Exception $e) {
                Log::error('Failed to get career insights: ' . $e->getMessage());
            }
        }

        return view('ai-recommendations', compact('recommendations', 'careerInsights'));
    }

    /**
     * Check AI configuration status
     */
    public function status()
    {
        $isConfigured = !empty(config('ai.openai_api_key'));
        $features = config('ai.features', []);

        return response()->json([
            'success' => true,
            'ai_configured' => $isConfigured,
            'model' => config('ai.model'),
            'features' => $features,
            'cache_duration' => config('ai.cache_duration'),
        ]);
    }
}
