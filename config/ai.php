<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | Your OpenAI API key for accessing GPT models. Get one from:
    | https://platform.openai.com/api-keys
    |
    */

    'openai_api_key' => env('OPENAI_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | AI Model
    |--------------------------------------------------------------------------
    |
    | The OpenAI model to use for recommendations.
    | Options: 'gpt-4', 'gpt-4-turbo-preview', 'gpt-3.5-turbo'
    |
    */

    'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),

    /*
    |--------------------------------------------------------------------------
    | Vision Model
    |--------------------------------------------------------------------------
    |
    | NOTE: This setting is deprecated. The system now uses custom AI only
    | for document validation. This config is kept for backward compatibility
    | but is not used.
    |
    */

    'vision_model' => null,

    /*
    |--------------------------------------------------------------------------
    | Temperature
    |--------------------------------------------------------------------------
    |
    | Controls randomness. Lower values (0.1-0.5) are more focused and
    | deterministic. Higher values (0.6-1.0) are more creative.
    |
    */

    'temperature' => env('OPENAI_TEMPERATURE', 0.7),

    /*
    |--------------------------------------------------------------------------
    | Max Tokens
    |--------------------------------------------------------------------------
    |
    | Maximum number of tokens to generate in the response.
    |
    */

    'max_tokens' => env('OPENAI_MAX_TOKENS', 1500),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | How long (in minutes) to cache AI recommendations for the same user.
    | Set to 0 to disable caching.
    |
    */

    'cache_duration' => env('AI_CACHE_DURATION', 60),

    /*
    |--------------------------------------------------------------------------
    | Recommendation Settings
    |--------------------------------------------------------------------------
    |
    | Configure how the AI recommendation system behaves.
    |
    */

    'recommendations' => [
        'max_jobs_to_analyze' => 50,  // Maximum number of jobs to send to AI
        'max_recommendations' => 10,   // Maximum recommendations to return
        'min_match_score' => 30,       // Minimum match score (0-100)
        'enable_explanations' => true, // Include AI explanation for each match
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific AI features.
    |
    */

    'features' => [
        // Keep only document validation enabled by default
        'job_matching' => env('AI_JOB_MATCHING', false),
        'resume_analysis' => env('AI_RESUME_ANALYSIS', false),
        'skill_suggestions' => env('AI_SKILL_SUGGESTIONS', false),
        'career_insights' => env('AI_CAREER_INSIGHTS', false),
        'document_validation' => env('AI_DOCUMENT_VALIDATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Validation Settings
    |--------------------------------------------------------------------------
    |
    | Configure AI-powered document validation for business permits and resumes.
    |
    */

    'document_validation' => [
        'business_permit' => [
            'enabled' => env('AI_VALIDATE_BUSINESS_PERMIT', true),
            'min_confidence' => env('AI_BUSINESS_PERMIT_MIN_CONFIDENCE', 80),
            'personal_email_min_confidence' => env('AI_PERSONAL_EMAIL_MIN_CONFIDENCE', 90), // Stricter for Gmail/Yahoo/etc
            'auto_approve_threshold' => 85,  // Auto-approve if confidence >= this value
            'auto_reject_threshold' => 50,   // Auto-reject if confidence < this value
            'require_manual_review_between' => true, // Require review for scores between thresholds
            'auto_delete_rejected' => env('AI_AUTO_DELETE_REJECTED', false), // Auto-delete rejected files
            'validation_delay_seconds' => env('AI_VALIDATION_DELAY', 10), // Delay before processing (allows user to complete signup)
            'personal_email_domains' => ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'], // Personal email providers
            // Allow a looser auto-approve path for certain officially-issued permit types
            'loose_auto_approve_threshold' => env('AI_BUSINESS_PERMIT_LOOSE_AUTO_APPROVE_THRESHOLD', 75),
            'loose_auto_approve_doc_types' => ['barangay', 'mayor', 'dti'],
        ],
        'resume' => [
            'enabled' => env('AI_VALIDATE_RESUME', false),
            'min_confidence' => env('AI_RESUME_MIN_CONFIDENCE', 70),
            'auto_approve_threshold' => 80,
            'auto_reject_threshold' => 40,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OCR Retry Settings
    |--------------------------------------------------------------------------
    |
    | Configure how many OCR retry attempts to perform and the delay between
    | attempts when a resume appears to be a scanned/low-quality PDF. These
    | retries are attempted before escalating the document for manual review
    | by administrators.
    |
    */
    'ocr' => [
        'retry_attempts' => env('AI_OCR_RETRY_ATTEMPTS', 2),
        'retry_delay_seconds' => env('AI_OCR_RETRY_DELAY', 15),
    ],
];
