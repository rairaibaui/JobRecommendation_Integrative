<?php

/**
 * Quick test script for AI Recommendations
 * Run with: php test-ai.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\AIRecommendationService;
use App\Models\User;
use App\Models\JobPosting;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== AI Recommendation System Test ===\n\n";

// Check configuration
echo "1. Checking AI Configuration...\n";
$apiKey = config('ai.openai_api_key');
$model = config('ai.model');
$isConfigured = !empty($apiKey);

echo "   API Key: " . ($isConfigured ? "✓ Configured" : "✗ Not configured") . "\n";
echo "   Model: {$model}\n";
echo "   Job Matching: " . (config('ai.features.job_matching') ? "Enabled" : "Disabled") . "\n";
echo "   Career Insights: " . (config('ai.features.career_insights') ? "Enabled" : "Disabled") . "\n";
echo "\n";

// Check database
echo "2. Checking Database...\n";
try {
    $userCount = User::where('user_type', 'job_seeker')->count();
    $jobCount = JobPosting::where('status', 'active')->count();
    echo "   Job Seekers: {$userCount}\n";
    echo "   Active Jobs: {$jobCount}\n";
    echo "   ✓ Database connection OK\n";
} catch (\Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
echo "\n";

// Test AI Service
echo "3. Testing AI Service...\n";
$aiService = app(AIRecommendationService::class);

if (!$isConfigured) {
    echo "   ⚠ OpenAI API key not configured\n";
    echo "   Testing fallback mode...\n";
}

// Get first job seeker
$testUser = User::where('user_type', 'job_seeker')->first();

if (!$testUser) {
    echo "   ✗ No job seekers found in database\n";
    echo "   Please create a job seeker account first\n";
    exit(1);
}

echo "   Testing with user: {$testUser->first_name} {$testUser->last_name}\n";
echo "   User skills: " . (is_array($testUser->skills) ? implode(', ', $testUser->skills) : $testUser->skills) . "\n";

// Get jobs
$jobs = JobPosting::active()->with('employer')->get();
echo "   Analyzing {$jobs->count()} jobs...\n";

try {
    $startTime = microtime(true);
    $recommendations = $aiService->getRecommendations($testUser, $jobs);
    $duration = round((microtime(true) - $startTime) * 1000, 2);
    
    echo "   ✓ Recommendations generated in {$duration}ms\n";
    echo "   Found " . count($recommendations) . " recommendations\n";
    
    if (count($recommendations) > 0) {
        echo "\n   Top 3 Recommendations:\n";
        foreach (array_slice($recommendations, 0, 3) as $i => $rec) {
            $num = $i + 1;
            echo "   {$num}. {$rec['title']} - Match: {$rec['match_score']}%\n";
            if (!empty($rec['ai_explanation'])) {
                echo "      Reason: " . substr($rec['ai_explanation'], 0, 100) . "...\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "   Check logs for details\n";
}
echo "\n";

// Test Career Insights
if (config('ai.features.career_insights') && $isConfigured) {
    echo "4. Testing Career Insights...\n";
    try {
        $insights = $aiService->getCareerInsights($testUser);
        echo "   ✓ Career insights generated\n";
        echo "   Preview: " . substr($insights, 0, 150) . "...\n";
    } catch (\Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Summary
echo "=== Test Summary ===\n";
if ($isConfigured) {
    echo "✓ AI system is configured and ready\n";
    echo "✓ You can now use AI-powered recommendations\n";
    echo "\nNext steps:\n";
    echo "- Visit /dashboard as a job seeker to see recommendations\n";
    echo "- Visit /ai/recommendations for detailed AI recommendations page\n";
    echo "- Check /ai/status endpoint for system status\n";
} else {
    echo "⚠ AI system is using fallback mode (basic skill matching)\n";
    echo "\nTo enable AI features:\n";
    echo "1. Get an API key from https://platform.openai.com/api-keys\n";
    echo "2. Add to .env: OPENAI_API_KEY=your-key-here\n";
    echo "3. Set AI_JOB_MATCHING=true in .env\n";
    echo "4. Run this test again\n";
}

echo "\n=== Test Complete ===\n";
