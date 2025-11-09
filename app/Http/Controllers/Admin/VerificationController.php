<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BusinessPermitValidated;
use App\Mail\ResumeVerified;
use App\Models\AuditLog;
use App\Models\AuditTrail;
use App\Models\DocumentValidation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Show unified verification management interface.
     */
    public function unified(Request $request)
    {
        // Default to resumes tab to show job seeker verifications first
        $activeTab = $request->input('tab', 'resumes');
        $status = $request->input('status');
        $search = $request->input('search');

        // Resume statistics
        $resumeStats = [
            'total' => User::where('user_type', 'job_seeker')->whereNotNull('resume_file')->count(),
            'verified' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'verified')->count(),
            'needs_review' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'needs_review')->count(),
            'pending' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'pending')->count(),
            'rejected' => User::where('user_type', 'job_seeker')->where('resume_verification_status', 'rejected')->count(),
        ];

        // Permit statistics
        $permitStats = [
            'total' => DocumentValidation::where('document_type', 'business_permit')->count(),
            'approved' => DocumentValidation::where('document_type', 'business_permit')->where('validation_status', 'approved')->count(),
            'pending' => DocumentValidation::where('document_type', 'business_permit')->where('validation_status', 'pending_review')->count(),
            'rejected' => DocumentValidation::where('document_type', 'business_permit')->where('validation_status', 'rejected')->count(),
        ];

        // Fetch items based on active tab
        if ($activeTab === 'resumes') {
            $query = User::where('user_type', 'job_seeker')->whereNotNull('resume_file');

            if ($status) {
                $query->where('resume_verification_status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            }

            $items = $query->orderByRaw("
                CASE resume_verification_status
                    WHEN 'needs_review' THEN 1
                    WHEN 'pending' THEN 2
                    WHEN 'verified' THEN 3
                    ELSE 4
                END
            ")->orderBy('updated_at', 'desc')->get();
        } else {
            $query = DocumentValidation::with('user')->where('document_type', 'business_permit');

            if ($status) {
                $query->where('validation_status', $status);
            }

            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            $items = $query->orderByRaw("
                CASE validation_status
                    WHEN 'pending_review' THEN 1
                    WHEN 'rejected' THEN 2
                    WHEN 'approved' THEN 3
                    ELSE 4
                END
            ")->orderBy('created_at', 'desc')->get();
        }

        return view('admin.unified-verifications', compact(
            'activeTab',
            'resumeStats',
            'permitStats',
            'items'
        ));
    }

    /**
     * Show admin verification dashboard - Redirect to unified view.
     */
    public function index(Request $request)
    {
        // Redirect to the new unified verification interface
        return redirect()->route('admin.verifications.unified', $request->all());
    }

    /**
     * Show admin verification dashboard (Legacy - for backwards compatibility).
     */
    public function indexLegacy(Request $request)
    {
        // Determine tab (business_permits or resumes)
        $tab = $request->input('tab', 'business_permits');

        if ($tab === 'resumes') {
            return $this->resumeVerifications($request);
        }

        // Default: Business Permits Tab
        $query = DocumentValidation::with('user')
            ->where('document_type', 'business_permit');

        // Filter by status if provided
        $status = $request->input('status');
        if ($status === null || $status === '' || $status === 'all') {
            // Default to pending if no specific status filter
            $query->where('validation_status', 'pending_review');
        } else {
            if ($status === 'expiring_soon') {
                // Special filter: permits expiring within next 30 days
                $query->whereNotNull('permit_expiry_date')
                    ->whereBetween('permit_expiry_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()]);
            } else {
                // Regular validation status filtering
                $query->where('validation_status', $status);
            }
        }

        // Search by company name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $pendingVerifications = $query->orderBy('created_at', 'desc')->get();

        // Statistics
        $approvedCount = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'approved')->count();
        $rejectedCount = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'rejected')->count();
        $pendingCount = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'pending_review')->count();
        $aiProcessedCount = DocumentValidation::where('document_type', 'business_permit')
            ->where('validated_by', 'ai')->count();
        $expiringSoonCount = DocumentValidation::where('document_type', 'business_permit')
            ->whereNotNull('permit_expiry_date')
            ->whereBetween('permit_expiry_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->count();
        $expiredCount = DocumentValidation::where('document_type', 'business_permit')
            ->whereNotNull('permit_expiry_date')
            ->where('permit_expiry_date', '<', now()->startOfDay())
            ->count();
        $adminUnreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        // For enabling/disabling Approve on rejected rows: which users have a new pending submission
        $usersWithPending = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'pending_review')
            ->pluck('user_id')
            ->unique()
            ->toArray();

        // Lists for admin dropdown/modal
        $expiringSoonList = DocumentValidation::with('user')
            ->where('document_type', 'business_permit')
            ->whereNotNull('permit_expiry_date')
            ->whereBetween('permit_expiry_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('permit_expiry_date', 'asc')
            ->take(50)
            ->get();

        $expiredList = DocumentValidation::with('user')
            ->where('document_type', 'business_permit')
            ->whereNotNull('permit_expiry_date')
            ->where('permit_expiry_date', '<', now()->startOfDay())
            ->orderBy('permit_expiry_date', 'desc')
            ->take(50)
            ->get();

        // Resume statistics for tab display
        $resumeNeedsReviewCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'needs_review')
            ->count();
        $resumeVerifiedCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'verified')
            ->count();
        $resumePendingCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'pending')
            ->count();

        return view('admin.verifications', compact(
            'pendingVerifications',
            'approvedCount',
            'rejectedCount',
            'pendingCount',
            'aiProcessedCount',
            'expiringSoonCount',
            'expiredCount',
            'expiringSoonList',
            'expiredList',
            'adminUnreadCount',
            'usersWithPending',
            'tab',
            'resumeNeedsReviewCount',
            'resumeVerifiedCount',
            'resumePendingCount'
        ));
    }

    /**
     * Show resume verifications (Job Seekers).
     */
    private function resumeVerifications(Request $request)
    {
        $query = User::where('user_type', 'job_seeker')
            ->whereNotNull('resume_file');

        // Filter by verification status
        $status = $request->input('status', 'needs_review');
        if ($status && $status !== 'all') {
            $query->where('resume_verification_status', $status);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        $resumeVerifications = $query->orderBy('updated_at', 'desc')->get();

        // Statistics
        $resumeNeedsReviewCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'needs_review')
            ->count();
        $resumeVerifiedCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'verified')
            ->count();
        $resumePendingCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'pending')
            ->count();
        $resumeRejectedCount = User::where('user_type', 'job_seeker')
            ->where('resume_verification_status', 'rejected')
            ->count();

        // Business permit stats for tab
        $pendingCount = DocumentValidation::where('document_type', 'business_permit')
            ->where('validation_status', 'pending_review')->count();
        $adminUnreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        $tab = 'resumes';

        return view('admin.resume-verifications', compact(
            'resumeVerifications',
            'resumeNeedsReviewCount',
            'resumeVerifiedCount',
            'resumePendingCount',
            'resumeRejectedCount',
            'pendingCount',
            'adminUnreadCount',
            'tab'
        ));
    }

    /**
     * Show individual verification details.
     */
    public function show($id)
    {
        $validation = DocumentValidation::with('user')->findOrFail($id);

        // Load audit trail
        $auditTrails = AuditTrail::where('validation_id', $validation->id)
            ->orderByDesc('created_at')
            ->get();

        // Log view event (best-effort, don't fail)
        try {
            $admin = Auth::user();
            AuditTrail::create([
                'validation_id' => $validation->id,
                'admin_id' => $admin?->id,
                'admin_email' => $admin?->email,
                'action' => 'view',
                'notes' => null,
                'metadata' => ['from' => 'admin.verifications.show'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log audit trail (view): '.$e->getMessage());
        }

        return view('admin.verification-detail', compact('validation', 'auditTrails'));
    }

    /**
     * Approve a verification.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'permit_expiry_date' => 'nullable|date|after:today',
            'admin_notes' => 'nullable|string|max:500',
            'override_duplicate' => 'nullable|boolean',
        ]);

        $validation = DocumentValidation::findOrFail($id);
        $user = User::findOrFail($validation->user_id);
        $admin = Auth::user();

        // Check if this is a duplicate override
        $isDuplicateOverride = false;
        if (isset($validation->ai_analysis['duplicate_detection'])) {
            $isDuplicateOverride = true;
        }

        $validation->validation_status = 'approved';
        $validation->is_valid = true;
        $validation->confidence_score = 100;
        $validation->validated_by = 'admin';
        $validation->validated_at = now();

        // Build approval reason
        $approvalReason = $request->admin_notes ?? 'Approved by administrator';
        if ($isDuplicateOverride) {
            $approvalReason .= ' (Duplicate override: Admin verified this is a legitimate case)';
        }
        $validation->reason = $approvalReason;

        // Set expiry date if provided by admin or from AI
        if ($request->has('permit_expiry_date')) {
            $validation->permit_expiry_date = $request->permit_expiry_date;
            $validation->expiry_reminder_sent = false;
        }

        // Store admin info in ai_analysis
        $aiAnalysis = $validation->ai_analysis ?? [];
        $aiAnalysis['admin_approval'] = [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'approved_at' => now()->toDateTimeString(),
            'notes' => $request->admin_notes,
            'duplicate_override' => $isDuplicateOverride,
        ];
        // Capture the business name snapshot that this approval is tied to
        $aiAnalysis['approved_company_name'] = $user->company_name;
        $validation->ai_analysis = $aiAnalysis;

        $validation->save();

        // Ensure only one approved permit is active per account: supersede older approvals
        try {
            DocumentValidation::where('user_id', $user->id)
                ->where('document_type', 'business_permit')
                ->where('validation_status', 'approved')
                ->where('id', '!=', $validation->id)
                ->update([
                    'validation_status' => 'rejected',
                    'is_valid' => false,
                    'reason' => 'Superseded by a newer approved business permit (ID '.$validation->id.').',
                ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to supersede prior approvals for user '.$user->id.': '.$e->getMessage());
        }

        // Create audit trail entry
        try {
            $expiry = $validation->permit_expiry_date;
            $expiryStr = $expiry instanceof \DateTimeInterface ? $expiry->format('Y-m-d') : ($expiry ?: null);
            AuditTrail::create([
                'validation_id' => $validation->id,
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'action' => 'approve',
                'notes' => $request->admin_notes,
                'metadata' => [
                    'duplicate_override' => $isDuplicateOverride,
                    'permit_expiry_date' => $expiryStr,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log audit trail (approve): '.$e->getMessage());
        }

        // Create audit log
        AuditLog::create([
            'user_id' => $admin->id,
            'event' => 'admin_permit_approved',
            'title' => 'Business Permit Approved',
            'description' => "Admin approved business permit for {$user->company_name}",
            'data' => json_encode([
                'validation_id' => $validation->id,
                'employer_id' => $user->id,
                'employer_email' => $user->email,
                'company_name' => $user->company_name,
                'duplicate_override' => $isDuplicateOverride,
                'admin_notes' => $request->admin_notes,
            ]),
        ]);

        // Notify employer
        Notification::create([
            'user_id' => $validation->user_id,
            'type' => 'success',
            'title' => 'Business Permit Approved',
            'message' => 'Your business permit has been approved! You can now post job listings.',
            'read' => false,
        ]);

        // Send email
        try {
            Mail::to($user->email)->send(new BusinessPermitValidated($user, $validation));
        } catch (\Exception $e) {
            // Email failed, but continue
            Log::warning("Failed to send approval email to {$user->email}: ".$e->getMessage());
        }

        return redirect()->route('admin.verifications.index')
            ->with('success', "Business permit approved for {$user->company_name}");
    }

    /**
     * Reject a verification.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $validation = DocumentValidation::findOrFail($id);
        $user = User::findOrFail($validation->user_id);
        $admin = Auth::user();

        $validation->validation_status = 'rejected';
        $validation->is_valid = false;
        $validation->confidence_score = 0;
        $validation->reason = $request->rejection_reason;
        $validation->validated_by = 'admin';
        $validation->validated_at = now();

        // Store admin info
        $aiAnalysis = $validation->ai_analysis ?? [];
        $aiAnalysis['admin_rejection'] = [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'rejected_at' => now()->toDateTimeString(),
            'reason' => $request->rejection_reason,
        ];
        $validation->ai_analysis = $aiAnalysis;

        $validation->save();

        // Create audit trail entry
        try {
            AuditTrail::create([
                'validation_id' => $validation->id,
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'action' => 'reject',
                'notes' => $request->rejection_reason,
                'metadata' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log audit trail (reject): '.$e->getMessage());
        }

        // Create audit log
        AuditLog::create([
            'user_id' => $admin->id,
            'event' => 'admin_permit_rejected',
            'title' => 'Business Permit Rejected',
            'description' => "Admin rejected business permit for {$user->company_name}",
            'data' => json_encode([
                'validation_id' => $validation->id,
                'employer_id' => $user->id,
                'employer_email' => $user->email,
                'company_name' => $user->company_name,
                'rejection_reason' => $request->rejection_reason,
            ]),
        ]);

        // Notify employer
        Notification::create([
            'user_id' => $validation->user_id,
            'type' => 'error',
            'title' => 'Business Permit Rejected',
            'message' => "Your business permit was rejected. Reason: {$request->rejection_reason}. Please upload a new, valid business permit.",
            'read' => false,
        ]);

        // Send email
        try {
            Mail::to($user->email)->send(new BusinessPermitValidated($user, $validation));
        } catch (\Exception $e) {
            Log::warning("Failed to send rejection email to {$user->email}: ".$e->getMessage());
        }

        return redirect()->route('admin.verifications.index')
            ->with('success', "Business permit rejected for {$user->company_name}. Employer has been notified to upload a new permit.");
    }

    /**
     * Stream the original document file for preview/download (admin only).
     */
    public function file(int $id)
    {
        $validation = DocumentValidation::with('user')->findOrFail($id);

        // Ensure file exists on public disk
        $path = $validation->file_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $absolute = Storage::disk('public')->path($path);
        $mime = mime_content_type($absolute) ?: 'application/octet-stream';

        // Log audit trail for file preview
        try {
            $admin = Auth::user();
            AuditTrail::create([
                'validation_id' => $validation->id,
                'admin_id' => $admin?->id,
                'admin_email' => $admin?->email,
                'action' => 'view_file',
                'notes' => null,
                'metadata' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log audit trail (view_file): '.$e->getMessage());
        }

        return response()->file($absolute, [
            'Content-Type' => $mime,
            // Prevent caching sensitive employer docs for too long
            'Cache-Control' => 'private, max-age=600',
        ]);
    }

    /**
     * Approve a job seeker's resume.
     */
    public function approveResume(Request $request, $userId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($userId);
        $admin = Auth::user();

        // Prevent approving a resume that was previously rejected and hasn't been replaced.
        if ($user->resume_verification_status === 'rejected') {
            // Try to detect which file was rejected (stored in verification_notes as a marker)
            $rejectedFile = null;
            if (!empty($user->verification_notes) && strpos($user->verification_notes, '||rejected_file:') !== false) {
                $parts = explode('||rejected_file:', $user->verification_notes);
                $rejectedFile = trim($parts[1] ?? '');
            }

            // If the rejected file matches the current resume file, block admin action.
            if ($rejectedFile !== null && $rejectedFile === ($user->resume_file ?? '')) {
                return redirect()->route('admin.verifications.index', ['tab' => 'resumes'])
                    ->with('error', 'Resume rejected. Waiting for new upload before review.');
            }
            // Otherwise allow (the job seeker uploaded a different file)
        }

        // Update resume verification status
        $user->resume_verification_status = 'verified';
        $user->verification_score = 100;
        $user->verified_at = now();
        $user->verification_notes = $request->admin_notes ?? 'Approved by administrator';

        // Clear any rejection flags
        $flags = json_decode($user->verification_flags, true) ?? [];
        $flags = array_filter($flags, function ($flag) {
            return $flag !== 'not_a_resume';
        });
        $user->verification_flags = json_encode($flags);

        $user->save();

        // Create audit log
        AuditLog::create([
            'user_id' => $admin->id,
            'event' => 'admin_resume_approved',
            'title' => 'Resume Approved',
            'description' => "Admin approved resume for job seeker {$user->email}",
            'data' => json_encode([
                'job_seeker_id' => $user->id,
                'email' => $user->email,
                'name' => trim($user->first_name.' '.$user->last_name),
                'admin_notes' => $request->admin_notes,
            ]),
        ]);

        // Notify job seeker
        Notification::create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'Resume Approved ✓',
            'message' => 'Congratulations! Your resume has been verified and approved by our administrator. You can now apply for jobs with confidence!',
            'read' => false,
            'data' => [
                'verification_status' => 'verified',
                'admin_notes' => $request->admin_notes,
            ],
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new ResumeVerified($user, 'verified', 100));
            Log::info("Resume approval email sent to {$user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send resume approval email: {$e->getMessage()}");
        }

        return redirect()->route('admin.verifications.index', ['tab' => 'resumes'])
            ->with('success', "Resume approved for {$user->first_name} {$user->last_name}. Notification and email sent.");
    }

    /**
     * Reject a job seeker's resume.
     */
    public function rejectResume(Request $request, $userId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($userId);
        $admin = Auth::user();

        // Prevent rejecting a resume that was already rejected (until a new upload exists)
        if ($user->resume_verification_status === 'rejected') {
            // If the previously rejected file is the same as current, block double-rejects.
            $rejectedFile = null;
            if (!empty($user->verification_notes) && strpos($user->verification_notes, '||rejected_file:') !== false) {
                $parts = explode('||rejected_file:', $user->verification_notes);
                $rejectedFile = trim($parts[1] ?? '');
            }

            if ($rejectedFile !== null && $rejectedFile === ($user->resume_file ?? '')) {
                return redirect()->route('admin.verifications.index', ['tab' => 'resumes'])
                    ->with('error', 'Resume already rejected. Waiting for new upload before review.');
            }
            // Otherwise continue and allow rejection of the new upload
        }

    // Update resume verification status
    $user->resume_verification_status = 'rejected';
    $user->verification_score = 0;
    // Persist the rejected file path inside notes so we can detect if a new upload occurred later
    $baseNote = 'Rejected by admin: '.$request->rejection_reason;
    $rejectedFileMarker = '||rejected_file:'.($user->resume_file ?? '');
    $user->verification_notes = $baseNote . $rejectedFileMarker;

        $user->save();

        // Create audit log
        AuditLog::create([
            'user_id' => $admin->id,
            'event' => 'admin_resume_rejected',
            'title' => 'Resume Rejected',
            'description' => "Admin rejected resume for job seeker {$user->email}",
            'data' => json_encode([
                'job_seeker_id' => $user->id,
                'email' => $user->email,
                'name' => trim($user->first_name.' '.$user->last_name),
                'rejection_reason' => $request->rejection_reason,
            ]),
        ]);

        // Notify job seeker
        Notification::create([
            'user_id' => $user->id,
            'type' => 'error',
            'title' => 'Resume Rejected ✗',
            'message' => "Your resume has been rejected. Reason: {$request->rejection_reason}. Please update and re-upload your resume in Settings.",
            'read' => false,
            'data' => [
                'rejection_reason' => $request->rejection_reason,
                'action_required' => 'upload_new_resume',
            ],
        ]);

        return redirect()->route('admin.verifications.index', ['tab' => 'resumes'])
            ->with('success', 'Resume rejected. Job seeker has been notified.');
    }

    /**
     * View/download job seeker's resume.
     */
    public function viewResume($userId)
    {
        $user = User::findOrFail($userId);

        if (!$user->resume_file || !Storage::disk('public')->exists($user->resume_file)) {
            abort(404, 'Resume file not found');
        }

        $absolutePath = Storage::disk('public')->path($user->resume_file);
        $mime = mime_content_type($absolutePath) ?: 'application/pdf';

        // Log audit trail for file preview
        try {
            $admin = Auth::user();
            AuditLog::create([
                'user_id' => $admin->id,
                'event' => 'admin_resume_viewed',
                'title' => 'Resume Viewed',
                'description' => "Admin viewed resume for {$user->email}",
                'data' => json_encode([
                    'job_seeker_id' => $user->id,
                    'email' => $user->email,
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log resume view: '.$e->getMessage());
        }

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, max-age=600',
        ]);
    }

    /**
     * Show detailed resume verification information for admin review.
     */
    public function resumeDetails($userId)
    {
        $user = User::findOrFail($userId);

        // Load latest verification log if available
        $latestLog = \App\Models\ResumeVerificationLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        return view('admin.resume-detail', compact('user', 'latestLog'));
    }
}
