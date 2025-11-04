<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_posting_id',
        'match_score',
        'explanation',
        'matching_skills',
        'career_growth',
        'rank',
        'viewed',
        'applied',
        'viewed_at',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'matching_skills' => 'array',
        'viewed' => 'boolean',
        'applied' => 'boolean',
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the recommendation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job posting for this recommendation
     */
    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Scope to get recommendations for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get high match recommendations
     */
    public function scopeHighMatch($query, $threshold = 70)
    {
        return $query->where('match_score', '>=', $threshold);
    }

    /**
     * Scope to get unviewed recommendations
     */
    public function scopeUnviewed($query)
    {
        return $query->where('viewed', false);
    }

    /**
     * Mark recommendation as viewed
     */
    public function markAsViewed()
    {
        $this->update([
            'viewed' => true,
            'viewed_at' => now(),
        ]);
    }

    /**
     * Mark recommendation as applied
     */
    public function markAsApplied()
    {
        $this->update([
            'applied' => true,
        ]);
    }
}
