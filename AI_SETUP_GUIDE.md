# AI-Powered Job Recommendation System - Setup Guide

## 🚀 Overview

Your job recommendation system now includes **AI-powered matching** using OpenAI's GPT models. The AI analyzes user profiles, skills, experience, and job requirements to provide intelligent job recommendations with explanations.

## ✨ Features

- **🤖 AI-Powered Job Matching**: Smart recommendations based on skills, experience, and career goals
- **📊 Match Scoring**: Each recommendation includes a 0-100 match score
- **💡 Personalized Explanations**: AI explains why each job is recommended
- **🎯 Career Insights**: Get AI-generated career advice and skill recommendations
- **⚡ Smart Caching**: Recommendations are cached to reduce API costs
- **🔄 Fallback System**: Works with basic skill matching if AI is unavailable

## 📦 Installation

### 1. Install Dependencies

The OpenAI PHP client has already been installed:

```bash
composer require openai-php/client
```

### 2. Configure Environment Variables

Copy the AI configuration from `.env.example` to your `.env` file:

```bash
# OpenAI API Configuration
OPENAI_API_KEY=your-openai-api-key-here
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=1500

# AI Features Configuration
AI_CACHE_DURATION=60
AI_JOB_MATCHING=true
AI_RESUME_ANALYSIS=true
AI_SKILL_SUGGESTIONS=true
AI_CAREER_INSIGHTS=true
```

### 3. Get Your OpenAI API Key

1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Sign up or log in
3. Navigate to [API Keys](https://platform.openai.com/api-keys)
4. Click "Create new secret key"
5. Copy the key and add it to your `.env` file

### 4. Run Database Migrations

```bash
php artisan migrate
```

This will create the `ai_recommendations` table to store AI-generated recommendations.

## 🎮 Usage

### For Job Seekers

Once configured, job seekers will automatically receive AI-powered recommendations on their dashboard:

1. **Dashboard**: AI recommendations appear automatically when viewing the dashboard
2. **Match Scores**: Each job shows a match score (0-100)
3. **AI Explanations**: Each recommendation includes why it's a good match
4. **Career Insights**: Access personalized career advice

### API Endpoints

#### Get AI Recommendations (JSON)
```
GET /ai/recommendations/api
```
Returns AI-powered job recommendations for the authenticated user.

#### Get Career Insights
```
GET /ai/career-insights
```
Get personalized career insights and suggestions.

#### Refresh Recommendations
```
POST /ai/recommendations/refresh
```
Clear cached recommendations and generate new ones.

#### Check AI Status
```
GET /ai/status
```
Check if AI is configured and which features are enabled.

### Web Routes

- `/ai/recommendations` - View AI recommendations page
- `/dashboard` - Dashboard with AI-powered job matches (for job seekers)

## ⚙️ Configuration

### AI Configuration (`config/ai.php`)

```php
'model' => 'gpt-3.5-turbo',        // AI model to use
'temperature' => 0.7,               // Creativity level (0-1)
'cache_duration' => 60,             // Cache duration in minutes
'recommendations' => [
    'max_jobs_to_analyze' => 50,    // Max jobs to send to AI
    'max_recommendations' => 10,     // Max recommendations to return
    'min_match_score' => 30,        // Minimum match score threshold
],
```

### Available Models

- `gpt-3.5-turbo` - Fast and cost-effective (recommended)
- `gpt-4` - More accurate but slower and more expensive
- `gpt-4-turbo-preview` - Latest GPT-4 model

### Cost Management

**Estimated Costs per Request:**
- GPT-3.5-turbo: ~$0.001 - $0.003 per recommendation generation
- GPT-4: ~$0.03 - $0.06 per recommendation generation

**Cost-Saving Tips:**
1. Enable caching (`AI_CACHE_DURATION=60`)
2. Use GPT-3.5-turbo for most cases
3. Limit `max_jobs_to_analyze` to 50 or less
4. Set reasonable cache duration (30-60 minutes)

## 🔧 Architecture

### Core Components

1. **AIRecommendationService** (`app/Services/AIRecommendationService.php`)
   - Main service handling AI interactions
   - Manages caching and fallback logic
   - Provides career insights

2. **AIRecommendationController** (`app/Http/Controllers/AIRecommendationController.php`)
   - Handles HTTP requests for AI features
   - Provides API endpoints

3. **DashboardController** (Updated)
   - Integrates AI recommendations into dashboard
   - Falls back to basic matching if AI unavailable

4. **AIRecommendation Model** (`app/Models/AIRecommendation.php`)
   - Stores recommendation history
   - Tracks user engagement (views, applications)

### How It Works

1. **User Profile Analysis**: AI analyzes user skills, experience, education, and summary
2. **Job Matching**: AI compares profile with available job postings
3. **Scoring**: Each job receives a match score (0-100)
4. **Explanation**: AI generates explanation for each recommendation
5. **Ranking**: Jobs sorted by match score
6. **Caching**: Results cached to reduce API calls

## 🧪 Testing

### Test Without OpenAI (Fallback Mode)

If `OPENAI_API_KEY` is not set, the system uses basic skill matching:

```bash
# Don't set OPENAI_API_KEY or set it to empty
OPENAI_API_KEY=
AI_JOB_MATCHING=false
```

### Test With OpenAI

```bash
# Set your API key
OPENAI_API_KEY=sk-your-key-here
AI_JOB_MATCHING=true

# Test the system
php artisan serve
```

Visit: `http://localhost:8000/dashboard` as a job seeker

## 📊 Monitoring

### Check AI Status

```php
// In your code
$aiService = app(\App\Services\AIRecommendationService::class);
// Service automatically logs errors to Laravel logs

// Via API
GET /ai/status
```

### Clear Cache

```php
// For a specific user
$aiService->clearCache($userId);

// Via API
POST /ai/recommendations/refresh
```

### View Logs

```bash
tail -f storage/logs/laravel.log
```

AI errors are logged with the `AI Recommendation Error:` prefix.

## 🔒 Security

1. **Never commit your API key** to version control
2. API keys are in `.env` which is `.gitignored`
3. Validate user authentication before AI requests
4. Rate limit API endpoints if needed

## 🐛 Troubleshooting

### "OpenAI API key is not configured"

**Solution**: Add `OPENAI_API_KEY` to your `.env` file

### "Failed to generate recommendations"

**Causes:**
- Invalid API key
- Insufficient OpenAI credits
- Network issues
- Rate limiting

**Solution**: Check logs in `storage/logs/laravel.log`

### Recommendations are cached/not updating

**Solution**: Clear cache
```bash
POST /ai/recommendations/refresh
```

### AI returns generic recommendations

**Cause**: Incomplete user profile

**Solution**: Ensure users complete their profiles:
- Skills
- Experience
- Education
- Summary/Bio

## 📈 Performance Tips

1. **Enable Caching**: Set `AI_CACHE_DURATION=60` (1 hour)
2. **Limit Job Analysis**: Set `max_jobs_to_analyze=50`
3. **Use GPT-3.5**: Much faster than GPT-4
4. **Async Processing**: Consider queue jobs for large user bases

## 🎯 Next Steps

### Recommended Enhancements

1. **Queue Processing**: Move AI processing to background jobs
   ```bash
   php artisan make:job GenerateAIRecommendations
   ```

2. **User Feedback**: Collect feedback on recommendations
   ```php
   Route::post('/ai/recommendations/{id}/feedback', ...);
   ```

3. **A/B Testing**: Compare AI vs. basic matching

4. **Analytics Dashboard**: Track recommendation accuracy

5. **Resume Parsing**: Extract skills from uploaded resumes using AI

6. **Email Notifications**: Send weekly AI-recommended jobs

## 💰 Cost Estimation

For a system with **1,000 active job seekers**:

- Recommendations per user: 1 per hour (with caching)
- Daily recommendations: 1,000 × 24 = 24,000
- Monthly cost (GPT-3.5): ~$72 - $216

**With proper caching (60 min):**
- Monthly cost: ~$36 - $108

## 📚 Additional Resources

- [OpenAI API Documentation](https://platform.openai.com/docs)
- [OpenAI PHP Client](https://github.com/openai-php/client)
- [Laravel Documentation](https://laravel.com/docs)

## 🆘 Support

If you encounter issues:

1. Check logs: `storage/logs/laravel.log`
2. Verify API key is valid
3. Test with fallback mode first
4. Check OpenAI account credits
5. Review error messages in browser console

---

**Your AI-powered job recommendation system is ready! 🎉**

Start by adding your OpenAI API key to `.env` and run the migrations.
