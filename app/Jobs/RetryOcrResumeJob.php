<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ResumeVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class RetryOcrResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $filePath;
    public $attemptsRemaining;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param string $filePath
     * @param int|null $attemptsRemaining
     */
    public function __construct(int $userId, string $filePath, ?int $attemptsRemaining = null)
    {
        $this->userId = $userId;
        $this->filePath = $filePath;
        $this->attemptsRemaining = $attemptsRemaining ?? config('ai.ocr.retry_attempts', 2);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $user = User::find($this->userId);
            if (!$user) {
                return;
            }

            // Diagnostic: ensure OCR binaries are available before attempting retries
            $diag = app(\App\Services\OcrDiagnosticService::class);
            $check = $diag->checkBinaries();
            if (!$check['ok']) {
                // Throttled admin warning
                $diag->warnAdminsOnce($check['missing']);

                // Mark user's resume as pending and annotate the notes about missing binaries
                try {
                    $user->resume_verification_status = 'pending';
                    $user->verification_flags = json_encode(array_merge((array)json_decode($user->verification_flags ?? '[]', true), ['ocr_tools_missing']));
                    $user->verification_notes = 'Automatic OCR retries cannot run because server OCR tools are missing: '.implode(', ', $check['missing']).'. An administrator has been notified.';
                    $user->save();

                    \App\Models\AuditLog::create([
                        'user_id' => $user->id,
                        'event' => 'resume_ocr_binaries_missing',
                        'title' => 'OCR Binaries Missing for Resume Processing',
                        'message' => 'Automatic OCR retries prevented due to missing binaries: '.implode(', ', $check['missing']),
                        'data' => json_encode(['missing' => $check['missing']]),
                    ]);
                } catch (\Throwable $__e) {
                    // best-effort
                }

                try {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'warning',
                        'title' => 'Resume Processing Delayed',
                        'message' => 'We were unable to perform automatic OCR retries for your resume because server OCR tools are not available. An administrator has been notified. You may re-upload a clearer resume.',
                        'read' => false,
                    ]);
                } catch (\Throwable $__n) {
                    // ignore
                }

                return;
            }

            /** @var ResumeVerificationService $svc */
            $svc = app(ResumeVerificationService::class);

            // Re-run verification on the stored resume file
            $result = $svc->verify($this->filePath, $user);

            $flags = array_map('strtolower', (array)($result['flags'] ?? []));

            $isScannedOrLowQuality = in_array('low_quality', $flags)
                || in_array('scanned_pdf', $flags)
                || in_array('image_only', $flags)
                || (!empty($result['is_scanned']) && $result['is_scanned'] === true);

            if (!$isScannedOrLowQuality && !in_array('not_a_resume', $flags)) {
                // Good extraction: persist verification results and notify user/admins as appropriate
                $user->resume_verification_status = $result['status'] ?? $user->resume_verification_status;
                $user->verification_flags = json_encode($result['flags'] ?? []);
                $user->verification_score = $result['score'] ?? $user->verification_score;
                $user->verified_at = $result['verified_at'] ?? $user->verified_at;
                $user->verification_notes = $result['notes'] ?? $user->verification_notes;
                $user->save();

                // Audit
                try {
                    \App\Models\AuditLog::create([
                        'user_id' => $user->id,
                        'event' => 'resume_ocr_retry_succeeded',
                        'title' => 'Resume OCR Retry Succeeded',
                        'message' => "Automatic OCR retry succeeded for {$user->email}.",
                        'data' => json_encode(['flags' => $result['flags'] ?? [], 'score' => $result['score'] ?? null]),
                    ]);
                } catch (\Throwable $__e) {
                    // best-effort
                }

                // Notify job seeker
                try {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'success',
                        'title' => 'Resume Verified',
                        'message' => 'Automatic OCR retry succeeded and your resume has been verified.',
                        'read' => false,
                        'data' => ['verification_status' => $user->resume_verification_status, 'score' => $user->verification_score],
                    ]);
                } catch (\Throwable $__n) {
                    // ignore
                }

                return;
            }

            // If still scanned/low-quality and we have attempts remaining, re-dispatch with delay
            if ($this->attemptsRemaining > 0) {
                $nextAttempts = $this->attemptsRemaining - 1;
                $delaySeconds = config('ai.ocr.retry_delay_seconds', 15);

                // Re-dispatch job with a delay
                self::dispatch($user->id, $this->filePath, $nextAttempts)->delay(now()->addSeconds($delaySeconds));

                // Audit log: another retry scheduled
                try {
                    \App\Models\AuditLog::create([
                        'user_id' => $user->id,
                        'event' => 'resume_ocr_retry_scheduled',
                        'title' => 'Resume OCR Retry Scheduled',
                        'message' => "Scheduled another OCR retry for {$user->email}. Attempts left: {$nextAttempts}",
                        'data' => json_encode(['attempts_left' => $nextAttempts]),
                    ]);
                } catch (\Throwable $__e) {
                    // best-effort
                }

                return;
            }

            // No attempts remaining: mark pending and notify admins for manual review
            $user->resume_verification_status = 'pending';
            $user->verification_flags = json_encode($result['flags'] ?? []);
            $user->verification_score = $result['score'] ?? $user->verification_score;
            $user->verification_notes = $result['notes'] ?? 'Automatic OCR retries failed; manual review required.';
            $user->save();

            try {
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'resume_pending_manual_review',
                    'title' => 'Resume Pending Manual Review',
                    'message' => "Automatic OCR retries exhausted for {$user->email}; manual review required.",
                    'data' => json_encode(['flags' => $result['flags'] ?? []]),
                ]);
            } catch (\Throwable $__e) {
                // best-effort
            }

            // Notify admins to review
            try {
                $admins = User::where('is_admin', true)->get();
                foreach ($admins as $admin) {
                    \App\Models\Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'warning',
                        'title' => 'Resume Requires Manual Review',
                        'message' => "Job seeker {$user->email} uploaded a resume that requires manual review after automatic OCR retries.",
                        'read' => false,
                        'data' => [
                            'job_seeker_id' => $user->id,
                            'email' => $user->email,
                            'flags' => $result['flags'] ?? [],
                        ],
                    ]);
                }
            } catch (\Throwable $__notifyEx) {
                // best-effort
            }

            // Notify the job seeker that their resume is pending review
            try {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'info',
                    'title' => 'Resume Pending Review',
                    'message' => 'We attempted automatic OCR retries but were unable to extract readable text. Your resume has been queued for manual review by our team. You may re-upload a clearer PDF to speed up verification.',
                    'read' => false,
                    'data' => [
                        'flags' => $result['flags'] ?? [],
                    ],
                ]);
            } catch (\Throwable $__userNotifyEx) {
                // best-effort
            }

        } catch (\Throwable $e) {
            Log::error('RetryOcrResumeJob failed: '.$e->getMessage());
        }
    }
}
