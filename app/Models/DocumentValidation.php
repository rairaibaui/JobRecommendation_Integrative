<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentValidation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'document_type',
        'file_path',
        'is_valid',
        'confidence_score',
        'validation_status',
        'reason',
        'ai_analysis',
        'validated_by',
        'validated_at',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_valid' => 'boolean',
        'confidence_score' => 'integer',
        'ai_analysis' => 'array',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the document validation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include validated documents.
     */
    public function scopeValidated($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope a query to only include pending review documents.
     */
    public function scopePendingReview($query)
    {
        return $query->where('validation_status', 'pending_review');
    }

    /**
     * Scope a query to only include rejected documents.
     */
    public function scopeRejected($query)
    {
        return $query->where('validation_status', 'rejected');
    }

    /**
     * Scope a query to only include approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('validation_status', 'approved');
    }

    /**
     * Scope a query to filter by document type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to filter by AI validation.
     */
    public function scopeAiValidated($query)
    {
        return $query->where('validated_by', 'ai');
    }

    /**
     * Scope a query to filter by manual validation.
     */
    public function scopeManuallyValidated($query)
    {
        return $query->where('validated_by', '!=', 'ai');
    }

    /**
     * Get validation confidence level description.
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->confidence_score >= 90) {
            return 'Very High';
        } elseif ($this->confidence_score >= 80) {
            return 'High';
        } elseif ($this->confidence_score >= 70) {
            return 'Medium';
        } elseif ($this->confidence_score >= 50) {
            return 'Low';
        } else {
            return 'Very Low';
        }
    }

    /**
     * Get human-readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->validation_status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'pending_review' => 'Pending Review',
            default => 'Unknown',
        };
    }

    /**
     * Get color code for status badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->validation_status) {
            'approved' => 'green',
            'rejected' => 'red',
            'pending_review' => 'yellow',
            default => 'gray',
        };
    }
}
