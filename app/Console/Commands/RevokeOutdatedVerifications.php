<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RevokeOutdatedVerifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:revoke-outdated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke resume verification for users who did not update their resume after contact change within the grace period.';

    public function handle()
    {
        // Prefer minute-level enforcement using resume_outdated_at timestamp.
        $minutes = config('verification.outdated_grace_minutes', 10);
        $threshold = now()->subMinutes($minutes);

        $this->info("Running revoke-outdated: grace_minutes={$minutes}, threshold={$threshold}");

        // Find users who were previously verified, have a resume_outdated_at timestamp,
        // and whose outdated timestamp is at or before the threshold. This mirrors the
        // logic in App\Jobs\AutoUnverifyResumeJob so the artisan command is a true
        // fallback when delayed queues aren't available.
        $users = User::where('resume_verification_status', 'verified')
            ->whereNotNull('resume_outdated_at')
            ->where('resume_outdated_at', '<=', $threshold)
            ->get();

        $this->info('Found '.$users->count().' user(s) to evaluate.');

        foreach ($users as $user) {
            try {
                // Double-check still outdated
                if (($user->resume_verification_status ?? null) !== 'outdated') continue;

                $user->resume_verification_status = 'pending';
                $user->verification_flags = null;
                $user->verification_score = 0;
                $user->verified_at = null;
                $user->verification_notes = 'Auto-unverified due to not updating resume after contact change.';
                // clear the resume_outdated_at marker now that we've revoked
                $user->resume_outdated_at = null;
                $user->save();

                // Notify the user (best-effort)
                try {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'error',
                        'title' => 'Resume Verification Revoked',
                        'message' => 'Your resume verification was revoked because you did not update your resume after changing your contact information. Please re-upload or update your resume to regain verification.',
                        'read' => false,
                        'data' => ['action' => 'reupload_resume'],
                    ]);
                } catch (\Throwable $__n) {
                    // best-effort
                }

                $this->info('Revoked verification for user '.$user->id);
                Log::info('RevokeOutdatedVerifications: revoked for user '.$user->id);
            } catch (\Throwable $e) {
                $this->error('Failed to process user '.$user->id.': '.$e->getMessage());
                Log::error('RevokeOutdatedVerifications failed for user '.$user->id.': '.$e->getMessage());
            }
        }

        $this->info('Completed revoke-outdated run.');

        return 0;
    }
}
