# 🤖 AI Integration - Complete Technical Overview

## Architecture Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USER LOGS IN                                 │
│                              ↓                                       │
│                    DashboardController                               │
│                              ↓                                       │
│              Is user a job seeker?                                   │
│                   ↙            ↘                                     │
│              YES                NO                                   │
│               ↓                  ↓                                   │
│      AI Integration      Show recent jobs                            │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                    AI RECOMMENDATION FLOW                            │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. Check Configuration                                              │
│     ├─ Is OPENAI_API_KEY set? ────→ NO → Use Basic Matching        │
│     ├─ Is AI_JOB_MATCHING=true? ──→ NO → Use Basic Matching        │
│     └─ YES to both ────────────────→ Continue to AI                 │
│                                                                      │
│  2. Check Cache (60 min default)                                     │
│     ├─ Cache exists? ──────────────→ YES → Return cached results    │
│     └─ NO ─────────────────────────→ Generate new recommendations   │
│                                                                      │
│  3. Generate AI Recommendations                                      │
│     ├─ Build User Profile                                            │
│     │   ├─ Skills (parsed from profile)                              │
│     │   ├─ Experience (years + work history)                         │
│     │   ├─ Education (level + details)                               │
│     │   ├─ Location preference                                       │
│     │   └─ Professional summary                                      │
│     │                                                                │
│     ├─ Build Jobs Data (up to 50 jobs)                               │
│     │   ├─ Job title & description                                   │
│     │   ├─ Required skills                                           │
│     │   ├─ Location & type                                           │
│     │   └─ Company info                                              │
│     │                                                                │
│     ├─ Create AI Prompt                                              │
│     │   └─ "Analyze this profile and recommend top 10 jobs..."       │
│     │                                                                │
│     ├─ Send to OpenAI API                                            │
│     │   ├─ Model: gpt-3.5-turbo (configurable)                       │
│     │   ├─ Temperature: 0.7 (creativity)                             │
│     │   └─ Max Tokens: 1500                                          │
│     │                                                                │
│     ├─ Receive AI Response (JSON)                                    │
│     │   ├─ job_id: 123                                               │
│     │   ├─ match_score: 85                                           │
│     │   ├─ explanation: "Great fit because..."                       │
│     │   ├─ matching_skills: ["PHP", "Laravel", "SQL"]                │
│     │   └─ career_growth: "High potential for advancement"           │
│     │                                                                │
│     ├─ Parse & Validate Response                                     │
│     │   ├─ Clean JSON (remove markdown)                              │
│     │   ├─ Match job IDs with database                               │
│     │   └─ Build final array with all job details                    │
│     │                                                                │
│     └─ Cache Results (save for 60 minutes)                           │
│                                                                      │
│  4. Fallback on Error                                                │
│     └─ If AI fails → Automatic switch to Basic Matching              │
│                                                                      │
│  5. Return to Dashboard                                              │
│     └─ Display jobs with match scores & explanations                 │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

## Key Files & Their Roles

### 1. **Configuration Layer**

#### `config/ai.php`
```php
// Controls all AI behavior
return [
    'openai_api_key' => env('OPENAI_API_KEY'),
    'model' => 'gpt-3.5-turbo',
    'temperature' => 0.7,
    'cache_duration' => 60,  // minutes
    'recommendations' => [
        'max_jobs_to_analyze' => 50,
        'max_recommendations' => 10,
        'min_match_score' => 30,
    ],
];
```

#### `.env`
```bash
OPENAI_API_KEY=sk-your-key-here
AI_JOB_MATCHING=true
AI_CACHE_DURATION=60
```

---

### 2. **Service Layer** (Brain of AI)

#### `app/Services/AIRecommendationService.php`

**What it does:**
1. **Connects to OpenAI** using the API key
2. **Builds intelligent prompts** from user data
3. **Calls GPT models** to analyze matches
4. **Parses AI responses** into usable data
5. **Handles errors gracefully** with fallback
6. **Caches results** to save money

**Key Methods:**

```php
getRecommendations($user, $jobs)
├─→ Check if AI is configured
├─→ Check cache
├─→ generateAIRecommendations()
│   ├─→ buildUserProfile()
│   ├─→ buildJobsData()
│   ├─→ buildPrompt()
│   ├─→ Call OpenAI API
│   └─→ parseAIResponse()
└─→ fallbackRecommendations() (if error)

getCareerInsights($user)
└─→ Get personalized career advice from AI

clearCache($userId)
└─→ Force refresh recommendations
```

---

### 3. **Controller Layer** (Integration Point)

#### `app/Http/Controllers/DashboardController.php`

**Changes Made:**
```php
class DashboardController extends Controller
{
    protected $aiService;  // ← ADDED

    public function __construct(AIRecommendationService $aiService)  // ← ADDED
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        // ... profile checks ...
        
        if ($user->user_type === 'job_seeker') {
            $allJobs = JobPosting::active()->with('employer')->get();
            
            // ← ADDED: AI Integration
            if (config('ai.features.job_matching') && config('ai.openai_api_key')) {
                try {
                    $jobs = $this->aiService->getRecommendations($user, $allJobs);
                } catch (\Exception $e) {
                    Log::error('AI failed: ' . $e->getMessage());
                    $jobs = $this->basicSkillMatching($user, $allJobs, $userSkills);
                }
            } else {
                $jobs = $this->basicSkillMatching($user, $allJobs, $userSkills);
            }
        }
        
        return view('dashboard', compact('jobs', ...));
    }
}
```

#### `app/Http/Controllers/AIRecommendationController.php`

**New Controller for AI Features:**
```php
// API Endpoints:
GET  /ai/recommendations        → View AI recommendations page
GET  /ai/recommendations/api    → Get recommendations as JSON
GET  /ai/career-insights         → Get career advice
POST /ai/recommendations/refresh → Clear cache, regenerate
GET  /ai/status                  → Check AI configuration
```

---

### 4. **Database Layer**

#### Migration: `2025_11_03_070842_create_ai_recommendations_table.php`

**Stores AI recommendations for analytics:**
```php
Schema::create('ai_recommendations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id');
    $table->foreignId('job_posting_id');
    $table->decimal('match_score', 5, 2);    // 0.00 to 100.00
    $table->text('explanation')->nullable();  // AI's explanation
    $table->json('matching_skills');          // Skills that matched
    $table->string('career_growth');          // Career potential
    $table->integer('rank');                  // Recommendation rank
    $table->boolean('viewed')->default(false);
    $table->boolean('applied')->default(false);
    $table->timestamp('viewed_at')->nullable();
    $table->timestamps();
});
```

#### Model: `app/Models/AIRecommendation.php`
```php
// Track user engagement with AI recommendations
$recommendation->markAsViewed();
$recommendation->markAsApplied();
```

---

### 5. **Routes Layer**

#### `routes/web.php`
```php
// Added AI routes:
Route::middleware('auth')->group(function () {
    Route::prefix('ai')->group(function () {
        Route::get('/recommendations', [AIRecommendationController::class, 'index']);
        Route::get('/recommendations/api', [AIRecommendationController::class, 'getRecommendations']);
        Route::get('/career-insights', [AIRecommendationController::class, 'getCareerInsights']);
        Route::post('/recommendations/refresh', [AIRecommendationController::class, 'refreshRecommendations']);
        Route::get('/status', [AIRecommendationController::class, 'status']);
    });
});
```

---

### 6. **View Layer**

#### `resources/views/ai-recommendations.blade.php`
Beautiful UI showing:
- Match percentage badges (0-100%)
- AI explanations for each job
- Matching skills highlighted
- Career growth insights
- Refresh button

---

## How AI Analysis Works

### Input to AI:
```json
{
  "user_profile": {
    "name": "John Doe",
    "skills": ["PHP", "Laravel", "JavaScript", "MySQL"],
    "experience": 3,
    "education": "Bachelor's Degree",
    "location": "Mandaluyong City",
    "summary": "Experienced web developer..."
  },
  "jobs": [
    {
      "id": 1,
      "title": "Senior PHP Developer",
      "skills": ["PHP", "Laravel", "Vue.js", "MySQL"],
      "description": "Looking for experienced developer..."
    }
  ]
}
```

### AI Prompt Sent to OpenAI:
```
You are an expert career advisor. Analyze this user profile and job postings.

USER PROFILE:
- Name: John Doe
- Skills: PHP, Laravel, JavaScript, MySQL
- Experience: 3 years
- Education: Bachelor's Degree
...

AVAILABLE JOBS:
Job ID 1:
- Title: Senior PHP Developer
- Skills: PHP, Laravel, Vue.js, MySQL
...

TASK: Provide top 10 job recommendations with:
- match_score (0-100)
- explanation (why it's a good fit)
- matching_skills
- career_growth potential

Return as JSON only.
```

### Output from AI:
```json
{
  "recommendations": [
    {
      "job_id": 1,
      "match_score": 92,
      "explanation": "Excellent match! You have 3 out of 4 required skills. Your Laravel and PHP expertise aligns perfectly with the job requirements. The missing Vue.js skill can be learned quickly given your JavaScript background.",
      "matching_skills": ["PHP", "Laravel", "MySQL"],
      "career_growth": "High potential for advancement to lead developer role within 2 years"
    }
  ]
}
```

---

## Smart Features Implemented

### 1. **Cost Optimization**
- ✅ **Caching**: Results cached for 60 minutes (configurable)
- ✅ **Job Limiting**: Only analyzes top 50 jobs (configurable)
- ✅ **Token Control**: Max 1500 tokens per request
- ✅ **Model Selection**: Uses GPT-3.5 by default (20x cheaper than GPT-4)

### 2. **Error Handling**
```php
try {
    $recommendations = $aiService->getRecommendations($user, $jobs);
} catch (\Exception $e) {
    Log::error('AI failed: ' . $e->getMessage());
    // Automatic fallback to basic skill matching
    $recommendations = basicSkillMatching($user, $jobs);
}
```

### 3. **Graceful Degradation**
```
┌─────────────────────────────────────┐
│ AI Recommendation Modes             │
├─────────────────────────────────────┤
│ 1. Full AI Mode                     │
│    ├─ API key configured ✓          │
│    ├─ Feature enabled ✓             │
│    └─ Returns: AI recommendations   │
│                                     │
│ 2. Fallback Mode (auto)             │
│    ├─ API fails/timeout             │
│    └─ Returns: Basic skill matching │
│                                     │
│ 3. Basic Mode (manual)              │
│    ├─ No API key configured         │
│    ├─ Feature disabled              │
│    └─ Returns: Basic skill matching │
└─────────────────────────────────────┘
```

### 4. **Data Structure**
Every job recommendation includes:
```php
[
    'id' => 123,
    'title' => 'Senior PHP Developer',
    'company' => 'Tech Corp',
    'location' => 'Mandaluyong City',
    'type' => 'Full-Time',
    'salary' => 'Php 80,000/month',
    'description' => '...',
    'skills' => ['PHP', 'Laravel', 'MySQL'],
    'match_score' => 92,                    // ← AI-generated
    'ai_explanation' => 'Great fit...',      // ← AI-generated
    'matching_skills' => ['PHP', 'Laravel'], // ← Highlighted
    'job_skills' => ['PHP', 'Laravel', ...], // ← Required skills
    'career_growth' => 'High potential',     // ← AI insight
    'employer_name' => 'John Smith',
    'posted_date' => 'Nov 3, 2025',
]
```

---

## Configuration Options

### Environment Variables:
```bash
# Required for AI
OPENAI_API_KEY=sk-your-key-here

# Optional (with defaults)
OPENAI_MODEL=gpt-3.5-turbo     # or gpt-4
OPENAI_TEMPERATURE=0.7          # 0.0-1.0
OPENAI_MAX_TOKENS=1500          # Response length
AI_CACHE_DURATION=60            # Minutes
AI_JOB_MATCHING=true            # Enable/disable
```

### Feature Flags:
```php
// config/ai.php
'features' => [
    'job_matching' => true,      // Main feature
    'resume_analysis' => true,   // Future
    'skill_suggestions' => true, // Future
    'career_insights' => true,   // Active
],
```

---

## Testing

### Without API Key (FREE):
```bash
# .env
OPENAI_API_KEY=
AI_JOB_MATCHING=false
```
→ Uses basic skill matching (works perfectly)

### With API Key:
```bash
# .env
OPENAI_API_KEY=sk-proj-xxxx
AI_JOB_MATCHING=true
```
→ Uses AI recommendations

### Test Script:
```bash
php test-ai.php
```

---

## Cost Estimation

### Per Recommendation Request:
- **GPT-3.5-turbo**: $0.001 - $0.003
- **GPT-4**: $0.03 - $0.06

### Monthly (100 users, 60-min cache):
- **GPT-3.5**: ~$30-90/month
- **GPT-4**: ~$600-1800/month

### Cost Savings with Caching:
- **No cache**: 24 requests/user/day
- **60-min cache**: 1-2 requests/user/day
- **Savings**: ~95% reduction in API costs

---

## How It All Connects

```
1. User visits /dashboard
   ↓
2. DashboardController checks user type
   ↓
3. If job_seeker → Call AIRecommendationService
   ↓
4. Service checks cache → If miss, call OpenAI
   ↓
5. OpenAI analyzes profile vs jobs
   ↓
6. Returns JSON with scores & explanations
   ↓
7. Service parses response
   ↓
8. Cache results for 60 minutes
   ↓
9. Return to controller
   ↓
10. Controller passes to view
    ↓
11. View displays jobs with AI insights
```

---

## Summary

**What AI Does:**
- Analyzes user profile comprehensively
- Compares against job requirements
- Generates match scores (0-100%)
- Explains why each job is a good fit
- Identifies matching skills
- Assesses career growth potential

**How It's Integrated:**
- Seamlessly into existing dashboard
- Automatic fallback if AI unavailable
- Smart caching to reduce costs
- Full API for custom integrations
- Analytics tracking for insights

**Benefits:**
- ✅ Smarter job recommendations
- ✅ Personalized explanations
- ✅ Works without AI (graceful degradation)
- ✅ Cost-effective (caching + GPT-3.5)
- ✅ Easy to configure
- ✅ Scalable architecture
