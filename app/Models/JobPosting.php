<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'company_name',
        'location',
        'type',
        'salary',
        'description',
        'skills',
        'status',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_posting_id');
    }

    // Scope for active jobs
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}