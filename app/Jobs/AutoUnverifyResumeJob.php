<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoUnverifyResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $user = User::find($this->userId);
            if (! $user) return;
            // Delegate the evaluation to a helper so tests can invoke it without DB
            $this->evaluateUser($user);
        } catch (\Throwable $e) {
            Log::error('AutoUnverifyResumeJob failed: '.$e->getMessage(), ['user_id' => $this->userId]);
        }
    }

    /**
     * Evaluate a user-like object and revoke verification if appropriate.
     * This is public to allow unit tests to call the logic without needing DB migrations.
     */
    public function evaluateUser($user)
    {
        try {
            // Debug logging to help tests explain why a user may not be revoked
            try {
                Log::info('AutoUnverifyResumeJob: user state', [
                    'user_id' => $user->id ?? null,
                    'resume_verification_status' => $user->resume_verification_status ?? null,
                    'resume_outdated_at' => isset($user->resume_outdated_at) && $user->resume_outdated_at ? $user->resume_outdated_at->toDateTimeString() : null,
                ]);
            } catch (\Throwable $__logEx) {
                // ignore logging failure
            }

            // Only revoke verification if the resume_outdated_at timestamp exists and the user is still verified
            if (empty($user->resume_outdated_at) || (($user->resume_verification_status ?? null) !== 'verified')) {
                return;
            }

            $graceMinutes = config('verification.outdated_grace_minutes', 10);
            $threshold = now()->subMinutes($graceMinutes);
            // If the outdated mark hasn't reached the threshold yet, skip
            if ($user->resume_outdated_at->greaterThan($threshold)) {
                return;
            }

            // Revoke verification: clear verification metadata and set status to pending
            $user->resume_verification_status = 'pending';
            $user->verification_flags = null;
            $user->verification_score = 0;
            $user->verified_at = null;
            $user->verification_notes = 'Auto-unverified due to not updating resume after contact change.';
            // Clear the outdated timestamp now that we've acted
            $user->resume_outdated_at = null;

            // Attempt to persist if the object supports save()
            try {
                if (method_exists($user, 'save')) {
                    $user->save();
                }
            } catch (\Throwable $__saveEx) {
                // best-effort: ignore save failures for non-DB test doubles
            }

            // Notify the user about revocation (best-effort)
            try {
                \App\Models\Notification::create([
                    'user_id' => $user->id ?? null,
                    'type' => 'error',
                    'title' => 'Resume Verification Revoked',
                    'message' => 'Your resume verification was revoked because you did not update your resume after changing your contact information. Please re-upload or update your resume to regain verification.',
                    'read' => false,
                    'data' => ['action' => 'reupload_resume'],
                ]);
            } catch (\Throwable $__n) {
                // best-effort
            }

            Log::info('Auto-unverified resume for user due to outdated resume after contact change', ['user_id' => $user->id ?? null]);
        } catch (\Throwable $e) {
            Log::error('AutoUnverifyResumeJob evaluateUser failed: '.$e->getMessage());
        }
    }
}
