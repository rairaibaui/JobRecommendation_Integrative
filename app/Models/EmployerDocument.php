<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerDocument extends Model
{
    protected $table = 'employer_documents';

    protected $fillable = [
        'employer_id',
        'file_path',
        'document_type',
        'owner_name',
        'issued_date',
        'valid_until',
        'status',
        'confidence_score',
        'raw_text',
        // legacy / optional fields
        'email',
        'fields',
        'has_signature',
        'review_reason',
        'reviewed_by_admin',
        'reviewed_at',
        'permit_expiry_date',
    ];

    protected $casts = [
        'fields' => 'array',
        'has_signature' => 'boolean',
        'issued_date' => 'date',
        'valid_until' => 'date',
        'permit_expiry_date' => 'date',
        'confidence_score' => 'float',
    ];

    /**
     * Scope to return documents that need manual review.
     */
    public function scopeForReview($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Whether the latest document requires the employer to re-upload.
     * True when the document was explicitly blocked or rejected by the system/admin.
     */
    public function needsReupload(): bool
    {
        $s = strtoupper($this->status ?? '');
        return in_array($s, ['BLOCKED', 'REJECTED'], true);
    }
}
