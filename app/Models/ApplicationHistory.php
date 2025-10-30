<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationHistory extends Model
{
    protected $table = 'application_history';

    protected $fillable = [
        'application_id',
        'employer_id',
        'job_seeker_id',
        'job_posting_id',
        'job_title',
        'company_name',
        'decision',
        'rejection_reason',
        'applicant_snapshot',
        'job_snapshot',
        'decision_date',
    ];

    protected $casts = [
        'applicant_snapshot' => 'array',
        'job_snapshot' => 'array',
        'decision_date' => 'datetime',
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function jobSeeker()
    {
        return $this->belongsTo(User::class, 'job_seeker_id');
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    // Scopes
    public function scopeHired($query)
    {
        return $query->where('decision', 'hired');
    }

    public function scopeRejected($query)
    {
        return $query->where('decision', 'rejected');
    }
}

