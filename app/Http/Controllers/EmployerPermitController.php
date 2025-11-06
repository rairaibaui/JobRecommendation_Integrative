<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\DocumentValidation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmployerPermitController extends Controller
{
    /**
     * Re-upload a rejected business permit (sets status to pending_review and notifies admins).
     */
    public function resubmitPermit(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user || ($user->user_type ?? null) !== 'employer') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'business_permit' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            // Store file under per-employer folder
            $folder = 'business_permits/'.$user->id;
            $permitPath = $request->file('business_permit')->store($folder, 'public');

            // Compute file hash
            $absolutePath = Storage::disk('public')->path($permitPath);
            $fileHash = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

            // Update user's current permit path for convenience
            $user->business_permit_path = $permitPath;
            $user->save();

            // Find the latest rejected record; if none, reuse pending; else create new
            $validation = DocumentValidation::where('user_id', $user->id)
                ->where('document_type', 'business_permit')
                ->orderByDesc('created_at')
                ->first();

            if ($validation && $validation->validation_status === 'rejected') {
                // Replace the rejected record with pending review and new file
                $validation->file_path = $permitPath;
                $validation->file_hash = $fileHash;
                $validation->is_valid = false;
                $validation->confidence_score = 0;
                $validation->validation_status = 'pending_review';
                $validation->reason = 'Re-uploaded by employer for re-verification.';
                $validation->validated_by = 'system';
                $validation->validated_at = now();
                $validation->permit_number = null;
                $validation->save();
            } elseif ($validation && $validation->validation_status === 'pending_review') {
                // Already pending: update file and keep status
                $validation->file_path = $permitPath;
                $validation->file_hash = $fileHash;
                $validation->reason = 'Updated file uploaded by employer while pending review.';
                $validation->validated_by = 'system';
                $validation->validated_at = now();
                $validation->permit_number = null;
                $validation->save();
            } else {
                // No existing record: create a fresh pending review record
                $validation = DocumentValidation::create([
                    'user_id' => $user->id,
                    'document_type' => 'business_permit',
                    'file_path' => $permitPath,
                    'file_hash' => $fileHash,
                    'is_valid' => false,
                    'confidence_score' => 0,
                    'validation_status' => 'pending_review',
                    'reason' => 'Re-uploaded by employer for re-verification.',
                    'ai_analysis' => null,
                    'validated_by' => 'system',
                    'validated_at' => now(),
                    'permit_number' => null,
                ]);
            }

            // Audit Trail
            try {
                AuditTrail::create([
                    'validation_id' => $validation->id,
                    'admin_id' => null,
                    'admin_email' => null,
                    'action' => 'reupload_permit',
                    'notes' => 'Employer re-uploaded permit from dashboard.',
                    'metadata' => [
                        'file_path' => $permitPath,
                        'file_hash' => $fileHash,
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('AuditTrail failed (reupload_permit): '.$e->getMessage());
            }

            // Notify employer
            Notification::create([
                'user_id' => $user->id,
                'type' => 'warning',
                'title' => 'Business Permit Re-uploaded',
                'message' => 'Your new business permit has been submitted for re-verification. You will be notified when the review is complete.',
                'read' => false,
                'data' => [
                    'validation_id' => $validation->id,
                    'company_name' => $user->company_name,
                    'email' => $user->email,
                ],
            ]);

            // Notify all admins via DB and email
            $admins = User::where('is_admin', true)->get(['id', 'email', 'first_name', 'last_name']);
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'warning',
                    'title' => 'Employer Re-uploaded Permit',
                    'message' => ($user->company_name ?: 'An employer').' re-uploaded their business permit for review.',
                    'read' => false,
                    'data' => [
                        'validation_id' => $validation->id,
                        'employer_id' => $user->id,
                        'company_name' => $user->company_name,
                        'email' => $user->email,
                    ],
                ]);

                try {
                    Mail::raw(
                        'Employer '.($user->company_name ?: $user->email).' has re-uploaded a business permit for re-verification.',
                        function ($message) use ($admin) {
                            $message->to($admin->email)
                                ->subject('Business Permit Re-uploaded');
                        }
                    );
                } catch (\Throwable $e) {
                    Log::warning('Admin email failed (reupload notice): '.$e->getMessage());
                }
            }

            // Email employer acknowledgement
            try {
                Mail::raw(
                    'We received your new business permit and it is now pending review. You will be notified once it is processed.',
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Your Business Permit Was Re-uploaded');
                    }
                );
            } catch (\Throwable $e) {
                Log::warning('Employer email failed (reupload ack): '.$e->getMessage());
            }

            return redirect()->route('employer.dashboard')
                ->with('success', 'Your new business permit has been sent for re-verification.');
        } catch (\Throwable $e) {
            Log::error('Permit re-upload failed: '.$e->getMessage());

            return redirect()->route('employer.dashboard')
                ->withErrors(['validation' => 'Failed to upload permit. Please try again.']);
        }
    }
}
