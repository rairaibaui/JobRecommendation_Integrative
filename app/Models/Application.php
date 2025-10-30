<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'job_posting_id',
        'employer_id',
        'job_title',
        'company_name',
        'status',
        'updated_by',
        'status_updated_at',
        'job_data',
            'resume_snapshot',
            'interview_date',
            'interview_notes',
            'interview_location'
    ];

    protected $casts = [
        'job_data' => 'array',
        'resume_snapshot' => 'array',
        'status_updated_at' => 'datetime',
            'interview_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function updateStatus($status, $updatedBy)
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => $status,
            'updated_by' => $updatedBy,
            'status_updated_at' => now(),
        ]);

        // Create notification for the applicant
        if ($oldStatus !== $status) {
            $this->notifyApplicant($status);
        }
    }

    protected function notifyApplicant($status)
    {
        $statusMessages = [
            'reviewing' => 'Your application is now under review!',
                'for_interview' => '📋 You have been shortlisted for an interview!',
                'interviewed' => 'Thank you for attending the interview. We will get back to you soon.',
            'accepted' => '🎉 Congratulations! Your application has been accepted!',
            'rejected' => 'Unfortunately, your application was not successful this time.',
        ];

        $message = $statusMessages[$status] ?? 'Your application status has been updated.';

        Notification::create([
            'user_id' => $this->user_id,
            'type' => 'application_status_changed',
            'title' => "Application Update: {$this->job_title}",
            'message' => $message,
            'data' => [
                'application_id' => $this->id,
                'job_title' => $this->job_title,
                'company_name' => $this->company_name,
                'status' => $status,
            ],
        ]);
    }
}

