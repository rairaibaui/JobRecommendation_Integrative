<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeVerificationLog extends Model
{
    use HasFactory;

    protected $table = 'resume_verification_logs';

    protected $fillable = [
        'user_id',
        'resume_path',
        'extracted_full_name',
        'extracted_email',
        'extracted_phone',
        'extracted_birthday',
        'match_name',
        'match_email',
        'match_phone',
        'match_birthday',
        'confidence_name',
        'confidence_email',
        'confidence_phone',
        'confidence_birthday',
        'overall_status',
        'notes',
        'raw_ai_response',
    ];

    protected $casts = [
        'match_name' => 'boolean',
        'match_email' => 'boolean',
        'match_phone' => 'boolean',
        'match_birthday' => 'boolean',
        'extracted_birthday' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
