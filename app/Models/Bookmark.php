<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'location',
        'type',
        'salary',
        'description',
        'skills',
        'company',
        'employer_name',
        'employer_email',
        'employer_phone',
        'posted_date',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
