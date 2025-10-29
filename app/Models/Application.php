<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id', 'job_title', 'job_data', 'resume_snapshot'
    ];

    protected $casts = [
        'job_data' => 'array',
        'resume_snapshot' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
