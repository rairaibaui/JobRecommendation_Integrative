<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class AdminNotificationService
{
    /**
     * Notify all admins when a job seeker uploads a resume.
     */
    public static function notifyResumeUploaded(User $jobSeeker)
    {
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'resume_uploaded',
                'title' => 'New Resume Uploaded',
                'message' => "Job seeker {$jobSeeker->name} ({$jobSeeker->email}) has uploaded a new resume for verification.",
                'link' => route('admin.verifications.unified', ['tab' => 'resumes']),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify all admins when an employer uploads a business permit.
     */
    public static function notifyPermitUploaded(User $employer)
    {
        $admins = User::where('user_type', 'admin')->get();
        $companyInfo = $employer->company_name ?? $employer->email;

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'permit_uploaded',
                'title' => 'New Business Permit Uploaded',
                'message' => "Employer {$employer->name} ({$companyInfo}) has uploaded a new business permit for verification.",
                'link' => route('admin.verifications.unified', ['tab' => 'permits']),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify all admins when a resume is updated/re-uploaded.
     */
    public static function notifyResumeUpdated(User $jobSeeker)
    {
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'resume_updated',
                'title' => 'Resume Updated',
                'message' => "Job seeker {$jobSeeker->name} ({$jobSeeker->email}) has updated their resume. Please review.",
                'link' => route('admin.verifications.unified', ['tab' => 'resumes']),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify all admins when a business permit is updated/re-uploaded.
     */
    public static function notifyPermitUpdated(User $employer)
    {
        $admins = User::where('user_type', 'admin')->get();
        $companyInfo = $employer->company_name ?? $employer->email;

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'permit_updated',
                'title' => 'Business Permit Updated',
                'message' => "Employer {$employer->name} ({$companyInfo}) has updated their business permit. Please review.",
                'link' => route('admin.verifications.unified', ['tab' => 'permits']),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify all admins when a business permit is auto-approved by AI.
     *
     * @param \App\Models\User $employer
     * @param mixed $validation DocumentValidation model or array with id/confidence
     * @param string $method 'strict' or 'loose' to indicate auto-approve path
     */
    public static function notifyPermitAutoApproved(User $employer, $validation, string $method = 'strict')
    {
        $admins = User::where('user_type', 'admin')->get();
        $companyInfo = $employer->company_name ?? $employer->email;

        $validationId = is_object($validation) && isset($validation->id) ? $validation->id : ($validation['id'] ?? null);
        $confidence = is_object($validation) && isset($validation->confidence_score) ? $validation->confidence_score : ($validation['confidence'] ?? null);

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'permit_auto_approved',
                'title' => 'Business Permit Auto-Approved',
                'message' => "Employer {$employer->name} ({$companyInfo}) had their business permit auto-approved by AI (method: {$method}). Confidence: " . ($confidence ?? 'N/A') . ".",
                'link' => route('admin.verifications.unified', ['tab' => 'permits', 'id' => $validationId]),
                'is_read' => false,
            ]);
        }
    }
}
