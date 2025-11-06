<?php

namespace App\Http\Controllers;

use App\Mail\ResumeVerified;
use App\Models\Application;
use App\Models\Hiring;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\DocumentValidationService;
use App\Services\ResumeVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $resumeVerification;
    protected $documentValidationService;

    public function __construct(ResumeVerificationService $resumeVerification, DocumentValidationService $documentValidationService)
    {
        $this->resumeVerification = $resumeVerification;
        $this->documentValidationService = $documentValidationService;
    }

    /**
     * Update only profile picture.
     */
    public function update(Request $request)
    {
        try {
            /** @var User $user */
            $user = User::find(Auth::id());
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validate all profile fields
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'birthday' => 'nullable|date',
                'phone_number' => 'nullable|string|max:20',
                'education_level' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'summary' => 'nullable|string',
                'education' => 'nullable|array',
                'experiences' => 'nullable|array',
                'languages' => 'nullable|string',
                'portfolio_links' => 'nullable|string',
                'availability' => 'nullable|string',
                'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'years_of_experience' => 'nullable|numeric|min:0',
                'location' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'remove_picture' => 'nullable|boolean',
            ]);

            // Track what was updated
            $pictureUpdated = false;
            $detailsUpdated = false;

            // Update profile details using fillable fields
            $data = $request->only([
                'first_name',
                'last_name',
                'phone_number',
                'summary',
                'languages',
                'portfolio_links',
                'availability',
                'education_level',
                'skills',
                'years_of_experience',
                'location',
                'address',
            ]);

            // Debug: Log the birthday value
            Log::info('Profile Update - Birthday from request:', [
                'birthday_in_data' => $data['birthday'] ?? 'not set',
                'birthday_from_request' => $request->birthday ?? 'not set',
                'all_request_data' => $request->all(),
            ]);

            // Handle education array
            if ($request->has('education')) {
                $education = collect($request->education)->map(function ($item) {
                    return is_string($item) ? json_decode($item, true) : $item;
                })->toArray();
                $data['education'] = $education;
            }

            // Handle experiences array
            if ($request->has('experiences')) {
                $experiences = collect($request->experiences)->map(function ($item) {
                    return is_string($item) ? json_decode($item, true) : $item;
                })->toArray();
                $data['experiences'] = $experiences;
            }

            $user->fill($data);
            $user->education_level = $request->education_level;
            $user->skills = $request->skills;
            $user->years_of_experience = $request->years_of_experience;
            $user->location = $request->location;
            $user->address = $request->address;

            // Explicitly set date_of_birth to ensure it's saved
            if ($request->has('birthday') && $request->birthday) {
                $user->date_of_birth = $request->birthday;
                Log::info('Date of birth explicitly set:', ['date_of_birth' => $user->date_of_birth]);
            }

            // Always mark as updated since we're handling arrays
            $detailsUpdated = true;

            // Handle profile picture
            if ($request->has('remove_picture') && $request->remove_picture) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $user->profile_picture = null;
                $pictureUpdated = true;
            }

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $path;
                $pictureUpdated = true;
            }

            // Handle resume file upload
            if ($request->hasFile('resume_file')) {
                // First, temporarily store and verify the file
                $tempPath = $request->file('resume_file')->store('temp_resumes', 'public');

                try {
                    // Verify if this is actually a resume before accepting it
                    $verificationResult = $this->resumeVerification->verify($tempPath, $user);

                    // Check if the document is not a resume
                    if (in_array('not_a_resume', $verificationResult['flags'])) {
                        // Delete the temp file
                        Storage::disk('public')->delete($tempPath);

                        // Log the incident for admin review
                        \App\Models\AuditLog::create([
                            'user_id' => $user->id,
                            'event' => 'resume_upload_rejected',
                            'title' => 'Non-Resume File Upload Attempted',
                            'message' => "User {$user->email} attempted to upload a non-resume PDF file. Upload was rejected by AI verification.",
                            'data' => json_encode([
                                'user_id' => $user->id,
                                'email' => $user->email,
                                'name' => trim($user->first_name.' '.$user->last_name),
                                'verification_score' => $verificationResult['score'],
                                'flags' => $verificationResult['flags'],
                                'notes' => $verificationResult['notes'],
                            ]),
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]);

                        // Notify admins about suspicious upload
                        $admins = User::where('is_admin', true)->get();
                        foreach ($admins as $admin) {
                            \App\Models\Notification::create([
                                'user_id' => $admin->id,
                                'type' => 'warning',
                                'title' => 'Non-Resume Upload Rejected',
                                'message' => "Job seeker {$user->email} attempted to upload a file that was not a resume. AI verification rejected the upload.",
                                'read' => false,
                                'data' => [
                                    'job_seeker_id' => $user->id,
                                    'email' => $user->email,
                                    'verification_score' => $verificationResult['score'],
                                    'reason' => $verificationResult['notes'],
                                ],
                            ]);
                        }

                        $errorMessage = 'The uploaded file does not appear to be a resume. Please upload a proper CV/Resume document with your education, experience, and contact information.';

                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => $errorMessage,
                            ], 422);
                        }

                        return redirect()->back()->withErrors(['resume_file' => $errorMessage])->withInput();
                    }

                    // If it's a valid resume, move it to the proper location
                    if ($user->resume_file && Storage::disk('public')->exists($user->resume_file)) {
                        Storage::disk('public')->delete($user->resume_file);
                    }

                    // Move from temp to permanent location
                    $finalPath = 'resumes/'.basename($tempPath);
                    Storage::disk('public')->move($tempPath, $finalPath);
                    $user->resume_file = $finalPath;

                    // Save verification results
                    $user->resume_verification_status = $verificationResult['status'];
                    $user->verification_flags = json_encode($verificationResult['flags']);
                    $user->verification_score = $verificationResult['score'];
                    $user->verified_at = $verificationResult['verified_at'];
                    $user->verification_notes = $verificationResult['notes'];

                    // Log successful upload for admin monitoring
                    \App\Models\AuditLog::create([
                        'user_id' => $user->id,
                        'event' => 'resume_uploaded',
                        'title' => 'Resume Uploaded and Verified',
                        'message' => "User {$user->email} successfully uploaded a resume. Verification status: {$verificationResult['status']}",
                        'data' => json_encode([
                            'user_id' => $user->id,
                            'email' => $user->email,
                            'verification_status' => $verificationResult['status'],
                            'verification_score' => $verificationResult['score'],
                            'flags' => $verificationResult['flags'],
                        ]),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);

                    // Notify admins about the new resume upload
                    AdminNotificationService::notifyResumeUploaded($user);

                    // CREATE NOTIFICATION FOR JOB SEEKER ABOUT THEIR RESUME STATUS
                    $jobSeekerNotificationData = [
                        'verification_status' => $verificationResult['status'],
                        'verification_score' => $verificationResult['score'],
                        'flags' => $verificationResult['flags'],
                    ];

                    if ($verificationResult['status'] === 'verified') {
                        \App\Models\Notification::create([
                            'user_id' => $user->id,
                            'type' => 'success',
                            'title' => 'Resume Verified ✓',
                            'message' => 'Great news! Your resume has been successfully verified and approved. You can now apply for jobs with confidence.',
                            'read' => false,
                            'data' => $jobSeekerNotificationData,
                        ]);

                        // Send email notification for verified resume
                        try {
                            Mail::to($user->email)->send(new ResumeVerified($user, 'verified', $verificationResult['score']));
                        } catch (\Exception $e) {
                            Log::error('Failed to send resume verified email: '.$e->getMessage());
                        }
                    } elseif ($verificationResult['status'] === 'needs_review') {
                        \App\Models\Notification::create([
                            'user_id' => $user->id,
                            'type' => 'warning',
                            'title' => 'Resume Under Review',
                            'message' => "Your resume has been uploaded and is currently under review by our admin team. You'll be notified once the review is complete.",
                            'read' => false,
                            'data' => $jobSeekerNotificationData,
                        ]);
                    } elseif ($verificationResult['status'] === 'incomplete') {
                        \App\Models\Notification::create([
                            'user_id' => $user->id,
                            'type' => 'warning',
                            'title' => 'Resume Incomplete',
                            'message' => 'Your resume was uploaded but appears to be missing some important information. Please review and upload a complete resume with your work experience, education, and contact details.',
                            'read' => false,
                            'data' => $jobSeekerNotificationData,
                        ]);
                    }

                    // Notify admins if resume needs review
                    if ($verificationResult['status'] === 'needs_review') {
                        $admins = User::where('is_admin', true)->get();
                        foreach ($admins as $admin) {
                            \App\Models\Notification::create([
                                'user_id' => $admin->id,
                                'type' => 'info',
                                'title' => 'Resume Needs Review',
                                'message' => "Job seeker {$user->email} uploaded a resume that needs manual review. Score: {$verificationResult['score']}/100",
                                'read' => false,
                                'data' => [
                                    'job_seeker_id' => $user->id,
                                    'email' => $user->email,
                                    'verification_score' => $verificationResult['score'],
                                    'flags' => $verificationResult['flags'],
                                    'notes' => $verificationResult['notes'],
                                ],
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Clean up temp file
                    if (Storage::disk('public')->exists($tempPath)) {
                        Storage::disk('public')->delete($tempPath);
                    }

                    Log::error('Resume verification failed: '.$e->getMessage());

                    // Notify admins about system error
                    try {
                        $admins = User::where('is_admin', true)->get();
                        foreach ($admins as $admin) {
                            \App\Models\Notification::create([
                                'user_id' => $admin->id,
                                'type' => 'error',
                                'title' => 'Resume Verification System Error',
                                'message' => "Resume verification failed for {$user->email}. Error: ".substr($e->getMessage(), 0, 100),
                                'read' => false,
                                'data' => [
                                    'job_seeker_id' => $user->id,
                                    'email' => $user->email,
                                    'error' => $e->getMessage(),
                                ],
                            ]);
                        }
                    } catch (\Exception $notifyError) {
                        // Best effort notification
                    }

                    $errorMessage = 'Failed to process resume file. Please make sure it\'s a valid PDF with readable text.';

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                        ], 422);
                    }

                    return redirect()->back()->withErrors(['resume_file' => $errorMessage])->withInput();
                }
            }

            try {
                $user->save();

                // Prepare message
                if ($pictureUpdated && $detailsUpdated) {
                    $message = 'Profile details and picture updated successfully.';
                } elseif ($pictureUpdated) {
                    $message = 'Profile picture updated successfully.';
                } else {
                    $message = 'Profile details updated successfully.';
                }

                // If the request expects JSON (AJAX), return JSON response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'user' => $user->fresh(),
                    ]);
                }

                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                Log::error('Profile update error: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save profile: '.$e->getMessage(),
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Profile update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return resume/profile as JSON for the apply flow.
     */
    public function resume(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'birthday' => $user->birthday,
            'location' => $user->location,
            'address' => $user->address,
            'summary' => $user->summary,
            'education' => $user->education ?? [],
            'experiences' => $user->experiences ?? [],
            'skills' => $user->skills,
            'languages' => $user->languages,
            'portfolio_links' => $user->portfolio_links,
            'availability' => $user->availability,
            'profile_picture' => $user->profile_picture ? asset('storage/'.$user->profile_picture) : null,
            'employment_status' => $user->employment_status ?? 'unemployed',
            'hired_by_company' => $user->hired_by_company,
            'hired_date' => $user->hired_date ? $user->hired_date->format('Y-m-d') : null,
            'resume_file' => $user->resume_file,
            'resume_verification_status' => $user->resume_verification_status ?? 'pending',
            'verification_score' => $user->verification_score ?? 0,
            'verification_flags' => $user->verification_flags,
            'verification_notes' => $user->verification_notes,
        ]);
    }

    public function changeEmail(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Email updated successfully.');
    }
    // Keep other methods (changeEmail, deactivate, etc.) as is

    /**
     * Job seeker resigns from current employment.
     */
    public function resign(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());
        if (!$user || ($user->user_type ?? null) !== 'job_seeker') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (($user->employment_status ?? 'unemployed') !== 'employed') {
            return redirect()->back()->with('success', 'Your status is already set to not employed.');
        }

        // Capture employer context from the last hire
        $lastHire = \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)
            ->where('decision', 'hired')
            ->orderByDesc('decision_date')
            ->first();

        $companyNameBefore = $user->hired_by_company;

        // Update employment fields
        $user->employment_status = 'unemployed';
        $user->hired_by_company = null;
        $user->hired_date = null;
        $user->save();

        // Record resignation in history for traceability
        \App\Models\ApplicationHistory::create([
            'application_id' => $lastHire->application_id ?? null,
            'employer_id' => $lastHire->employer_id ?? null,
            'job_seeker_id' => $user->id,
            'job_posting_id' => $lastHire->job_posting_id ?? null,
            'job_title' => $lastHire->job_title ?? null,
            'company_name' => $companyNameBefore,
            'decision' => 'resigned',
            'rejection_reason' => $request->input('reason'),
            'applicant_snapshot' => $lastHire->applicant_snapshot ?? null,
            'job_snapshot' => $lastHire->job_snapshot ?? null,
            'decision_date' => now(),
        ]);

        // Notify the employer about the resignation
        if ($lastHire && $lastHire->employer_id) {
            $employerUser = User::find($lastHire->employer_id);
            if ($employerUser) {
                $userName = trim($user->first_name.' '.$user->last_name) ?: $user->email;
                $jobTitle = $lastHire->job_title ?? 'Unknown Position';

                \App\Models\Notification::create([
                    'user_id' => $employerUser->id,
                    'type' => 'employee_resigned',
                    'title' => 'Employee Resigned',
                    'message' => "{$userName} has resigned from the position of {$jobTitle}.",
                    'read' => false,
                ]);
            }
        }

        // Also notify the job seeker (self) that the resignation was recorded
        try {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'resignation_recorded',
                'title' => 'Resignation Submitted',
                'message' => $companyNameBefore
                    ? "Your resignation from {$companyNameBefore} has been recorded. You are now set to Seeking Employment."
                    : 'Your resignation has been recorded. You are now set to Seeking Employment.',
                'data' => [
                    'company_name' => $companyNameBefore,
                    'reason' => $request->input('reason'),
                ],
                'read' => false,
            ]);
        } catch (\Throwable $e) {
            // best effort; do not fail flow
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'You have resigned. Your status is now set to actively seeking.']);
        }

        return redirect()->back()->with('success', 'You have resigned. You can now apply for new job postings.');
    }

    /**
     * Update employer profile (company details).
     */
    public function updateEmployer(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'company_description' => 'nullable|string|max:2000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'business_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'remove_picture' => 'nullable|boolean',
        ]);

        // Enforce one-permit-per-account: if there is an approved permit, prevent changing the company name
        $existingApprovedValidation = \App\Models\DocumentValidation::where('user_id', $user->id)
            ->where('document_type', 'business_permit')
            ->where('validation_status', 'approved')
            ->orderByDesc('created_at')
            ->first();

        if ($existingApprovedValidation && !$request->hasFile('business_permit')) {
            // They are NOT uploading a new permit—block company name change
            $approvedCompanyName = $existingApprovedValidation->ai_analysis['approved_company_name'] ?? null;
            if ($approvedCompanyName && $request->company_name !== $approvedCompanyName) {
                return back()->withErrors([
                    'company_name' => "Your verified business permit is tied to '{$approvedCompanyName}'. You cannot change your business name unless you upload a new permit. To operate a different business, register a separate employer account.",
                ]);
            }
        }

        $user->company_name = $request->company_name;
        if ($request->filled('first_name')) {
            $user->first_name = $request->first_name;
        }
        if ($request->filled('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->filled('job_title')) {
            $user->job_title = $request->job_title;
        }
        $user->phone_number = $request->phone_number; // required
        $user->address = $request->address;
        $user->company_description = $request->company_description;

        // Handle profile picture removal
        if ($request->has('remove_picture') && $request->remove_picture) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = null;
        }

        // Company logo/profile picture
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // Business permit upload with background AI validation
        if ($request->hasFile('business_permit')) {
            // Delete old business permit if exists
            if ($user->business_permit_path && Storage::disk('public')->exists($user->business_permit_path)) {
                Storage::disk('public')->delete($user->business_permit_path);
            }

            // Store new permit immediately
            $permitPath = $request->file('business_permit')->store('business_permits/'.$user->id, 'public');
            $user->business_permit_path = $permitPath;

            // Queue AI validation for background processing
            $isDocumentValidationEnabled = config('ai.features.document_validation', false)
                                           && config('ai.document_validation.business_permit.enabled', false);

            if ($isDocumentValidationEnabled) {
                $delay = config('ai.document_validation.business_permit.validation_delay_seconds', 10);

                \App\Jobs\ValidateBusinessPermitJob::dispatch(
                    $user->id,
                    $permitPath,
                    [
                        'company_name' => $user->company_name ?? 'Unknown',
                        'email' => $user->email,
                    ]
                )->delay(now()->addSeconds($delay));
            } else {
                // If AI validation is disabled or no queue worker, immediately create a pending review record
                try {
                    $absolutePath = Storage::disk('public')->path($permitPath);
                    $fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

                    $pending = new \App\Models\DocumentValidation([
                        'user_id' => $user->id,
                        'document_type' => 'business_permit',
                        'file_path' => $permitPath,
                        'file_hash' => $fileHash,
                        'is_valid' => false,
                        'confidence_score' => 0,
                        'validation_status' => 'pending_review',
                        'reason' => 'Uploaded by employer. Awaiting manual review by administrator.',
                        'ai_analysis' => null,
                        'validated_by' => 'system',
                        'validated_at' => now(),
                        'permit_expiry_date' => null,
                        'expiry_reminder_sent' => false,
                    ]);
                    $pending->save();

                    // Notify employer
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'warning',
                        'title' => 'Business Permit Submitted',
                        'message' => 'Your business permit was received and is pending review by an administrator.',
                        'read' => false,
                        'data' => [
                            'validation_id' => $pending->id,
                            'company_name' => $user->company_name,
                            'email' => $user->email,
                        ],
                    ]);

                    // Notify all admins to review
                    $admins = User::where('is_admin', true)->get(['id', 'email']);
                    foreach ($admins as $admin) {
                        \App\Models\Notification::create([
                            'user_id' => $admin->id,
                            'type' => 'warning',
                            'title' => 'New Business Permit Uploaded',
                            'message' => ($user->company_name ?: 'An employer').' uploaded a new business permit. Please review.',
                            'read' => false,
                            'data' => [
                                'validation_id' => $pending->id,
                                'employer_id' => $user->id,
                                'company_name' => $user->company_name,
                                'email' => $user->email,
                            ],
                        ]);
                    }
                } catch (\Throwable $e) {
                    // Best-effort: do not block profile update if this fails
                    Log::warning('Failed to create pending DocumentValidation: '.$e->getMessage());
                }
            }
        }

        $user->save();

        // ONLY reset verification if a NEW business permit was uploaded (not for profile updates)
        if ($request->hasFile('business_permit')) {
            // Notify admins about the permit upload/update
            AdminNotificationService::notifyPermitUpdated($user);

            $latestValidation = \App\Models\DocumentValidation::where('user_id', $user->id)
                ->where('document_type', 'business_permit')
                ->orderByDesc('created_at')
                ->first();

            if ($latestValidation && $latestValidation->validation_status === 'approved') {
                $latestValidation->validation_status = 'pending_review';
                $latestValidation->reason = 'New business permit uploaded. Re-verification required.';
                $latestValidation->save();

                // Create notification for employer (idempotent of earlier notification)
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'warning',
                    'title' => 'Re-verification Required',
                    'message' => 'You uploaded a new business permit. Your verification status has been reset to "Pending Review" until an administrator validates the new document.',
                    'read' => false,
                ]);

                // Log audit for admin
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'business_permit_uploaded',
                    'title' => 'New Business Permit Uploaded',
                    'message' => "Employer uploaded new business permit - re-verification triggered. Company: {$user->company_name}",
                    'data' => json_encode([
                        'company_name' => $user->company_name,
                        'file_path' => $request->hasFile('business_permit') ? $permitPath : null,
                    ]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Employer profile updated successfully.']);
        }

        $successMessage = 'Employer profile updated successfully.';
        if ($request->hasFile('business_permit')) {
            $successMessage .= ' Your new business permit has been uploaded and is being verified. You will be notified once the review is complete.';
        }

        return redirect()->route('settings')->with('success', $successMessage);
    }

    /**
     * Permanently delete the authenticated user's account and related data.
     * After deletion, logs out and redirects to login so a new account can be created.
     */
    public function destroyAccount(Request $request)
    {
        /** @var User $user */
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            DB::transaction(function () use ($user) {
                // If employer, delete their job postings and related applications/history
                if (($user->user_type ?? null) === 'employer') {
                    $jobIds = \App\Models\JobPosting::where('employer_id', $user->id)->pluck('id');

                    if ($jobIds->isNotEmpty()) {
                        \App\Models\Application::whereIn('job_posting_id', $jobIds)->delete();
                        \App\Models\ApplicationHistory::whereIn('job_posting_id', $jobIds)->delete();
                        \App\Models\JobPosting::whereIn('id', $jobIds)->delete();
                    }

                    // Applications referencing this employer directly
                    \App\Models\Application::where('employer_id', $user->id)->delete();
                    \App\Models\ApplicationHistory::where('employer_id', $user->id)->delete();
                } else {
                    // Job seeker: delete their applications and history
                    \App\Models\Application::where('user_id', $user->id)->delete();
                    \App\Models\ApplicationHistory::where('job_seeker_id', $user->id)->delete();
                }

                // Common: bookmarks, notifications, audit logs
                \App\Models\Bookmark::where('user_id', $user->id)->delete();
                \App\Models\Notification::where('user_id', $user->id)->delete();
                \App\Models\AuditLog::where('user_id', $user->id)->orWhere('actor_id', $user->id)->delete();

                // Remove stored files
                try {
                    if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                        Storage::disk('public')->delete($user->profile_picture);
                    }
                    if ($user->resume_file && Storage::disk('public')->exists($user->resume_file)) {
                        Storage::disk('public')->delete($user->resume_file);
                    }
                    if ($user->business_permit_path && Storage::disk('public')->exists($user->business_permit_path)) {
                        Storage::disk('public')->delete($user->business_permit_path);
                    }
                } catch (\Throwable $e) {
                    // Best effort file cleanup
                }

                // Finally, delete the user
                $user->delete();
            });
        } catch (\Throwable $e) {
            Log::error('Account deletion failed: '.$e->getMessage());

            return redirect()->back()->withErrors(['delete' => 'Failed to delete account. Please try again.']);
        }

        // Logout and invalidate session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deleted.');
    }

    /**
     * Send OTP for phone number verification.
     */
    public function sendPhoneOTP(Request $request)
    {
        $request->validate([
            'new_phone_number' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $phoneService = app(\App\Services\PhoneVerificationService::class);

        $result = $phoneService->sendOTP($user, $request->new_phone_number);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }

    /**
     * Verify OTP and update phone number.
     */
    public function verifyPhoneOTP(Request $request)
    {
        $request->validate([
            'new_phone_number' => 'required|string|max:20',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $phoneService = app(\App\Services\PhoneVerificationService::class);

        $result = $phoneService->verifyOTP($user, $request->new_phone_number, $request->otp_code);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }
}
