<?php

namespace App\Console\Commands;

use App\Jobs\ValidateBusinessPermitJob;
use App\Models\DocumentValidation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RevalidateLegacyEmployers extends Command
{
    protected $signature = 'revalidate:legacy-employers 
                            {--action=review : Action to take - review, ai-validate, revoke, or delete}
                            {--force : Skip confirmation prompts}';

    protected $description = 'Handle legacy employer accounts that were auto-approved without AI validation';

    public function handle()
    {
        $this->info('Checking legacy employer accounts...');
        $this->newLine();

        // Find all validations marked as legacy (system-validated)
        $legacyValidations = DocumentValidation::where('validated_by', 'system')
            ->where('document_type', 'business_permit')
            ->where('reason', 'LIKE', '%Legacy account%')
            ->with('user')
            ->get();

        if ($legacyValidations->isEmpty()) {
            $this->info('No legacy accounts found.');

            return 0;
        }

        $this->warn("Found {$legacyValidations->count()} legacy employer accounts:");
        $this->newLine();

        // Show summary
        foreach ($legacyValidations as $validation) {
            $user = $validation->user;
            if (!$user) {
                continue;
            }

            $hasFile = $validation->file_path && Storage::disk('public')->exists($validation->file_path);

            $this->line("ID: {$user->id} | {$user->email} | {$user->company_name}");
            $this->line('  Permit: '.($hasFile ? "✓ File exists: {$validation->file_path}" : '✗ No file'));
        }

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $action = $this->option('action');

        switch ($action) {
            case 'review':
                return $this->markForReview($legacyValidations);

            case 'ai-validate':
                $this->error('AI revalidation has been disabled in this deployment.');
                $this->line('Use --action=review to mark for manual review, or --action=revoke to revoke approvals.');
                return 1;

            case 'revoke':
                return $this->revokeApproval($legacyValidations);

            case 'delete':
                return $this->deleteAccounts($legacyValidations);

            default:
                $this->showOptions();

                return 0;
        }
    }

    protected function showOptions()
    {
        $this->newLine();
        $this->info('Available Actions:');
        $this->newLine();

        $this->line('1. <fg=yellow>Mark for Manual Review</> (Safest)');
        $this->line('   php artisan revalidate:legacy-employers --action=review');
        $this->line('   → Changes status to pending_review, admin must approve');
        $this->newLine();

        $this->line('2. <fg=cyan>Run AI Validation</> (Recommended)');
        $this->line('   php artisan revalidate:legacy-employers --action=ai-validate');
        $this->line('   → Re-validates all permits with AI, auto-approve/reject based on results');
        $this->newLine();

        $this->line('3. <fg=red>Revoke Approval</> (Strict)');
        $this->line('   php artisan revalidate:legacy-employers --action=revoke');
        $this->line('   → Marks all as rejected, they cannot post jobs until re-verified');
        $this->newLine();

        $this->line('4. <fg=red;options=bold>Delete Accounts</> (Nuclear Option)');
        $this->line('   php artisan revalidate:legacy-employers --action=delete --force');
        $this->line('   → Permanently deletes employer accounts with fake permits');
        $this->newLine();
    }

    protected function markForReview($validations)
    {
        if (!$this->option('force')) {
            if (!$this->confirm("Mark {$validations->count()} legacy accounts for manual review?")) {
                return 0;
            }
        }

        $this->info('Marking accounts for manual review...');

        foreach ($validations as $validation) {
            $validation->update([
                'validation_status' => 'pending_review',
                'is_valid' => false,
                'reason' => 'Legacy account - requires manual review of business permit',
                'ai_analysis' => array_merge(
                    $validation->ai_analysis ?? [],
                    ['flagged_for_review' => now()->toDateTimeString(), 'reason' => 'Auto-approved legacy account']
                ),
            ]);

            $this->line("<fg=yellow>✓</> {$validation->user->email} → Pending Review");
        }

        $this->newLine();
        $this->warn('All legacy accounts now require manual review before they can post jobs.');
        $this->line('Admins must review and approve each business permit.');

        return 0;
    }

    protected function runAIValidation($validations)
    {
        if (!$this->option('force')) {
            if (!$this->confirm("Run AI validation on {$validations->count()} business permits? (Uses OpenAI API - costs apply)")) {
                return 0;
            }
        }

        $aiEnabled = config('ai.features.document_validation', false)
                     && config('ai.document_validation.business_permit.enabled', false);

        if (!$aiEnabled) {
            $this->error('AI document validation is not enabled in config/ai.php');
            $this->line('Enable it in .env: AI_DOCUMENT_VALIDATION=true');

            return 1;
        }

        $this->info('Queueing AI validation jobs...');
        $queued = 0;
        $skipped = 0;

        foreach ($validations as $validation) {
            $user = $validation->user;
            if (!$user) {
                ++$skipped;
                continue;
            }

            // Check if file exists
            if (!$validation->file_path || !Storage::disk('public')->exists($validation->file_path)) {
                $this->line("<fg=red>✗</> {$user->email} - No permit file, marking as rejected");

                $validation->update([
                    'validation_status' => 'rejected',
                    'is_valid' => false,
                    'confidence_score' => 0,
                    'reason' => 'No business permit file found',
                ]);

                ++$skipped;
                continue;
            }

            // Delete old validation, will be recreated by job
            $filePath = $validation->file_path;
            $validation->delete();

            // Check if personal email for stricter validation
            $isPersonalEmail = preg_match('/@(gmail|yahoo|hotmail|outlook)\.com$/i', $user->email);

            // Queue AI validation job
            ValidateBusinessPermitJob::dispatch(
                $user->id,
                $filePath,
                [
                    'is_personal_email' => $isPersonalEmail,
                    'revalidation' => true,
                    'original_status' => 'legacy_account',
                ]
            )->delay(now()->addSeconds($queued * 5)); // Stagger by 5 seconds each

            $this->line("<fg=cyan>✓</> {$user->email} - Queued for AI validation".($isPersonalEmail ? ' (personal email - stricter)' : ''));
            ++$queued;
        }

        $this->newLine();
        $this->info("Queued {$queued} accounts for AI validation");
        if ($skipped > 0) {
            $this->warn("Skipped {$skipped} accounts (no file or missing user)");
        }

        $this->newLine();
        $this->line('Make sure queue worker is running:');
        $this->line('  php artisan queue:work --tries=3');
        $this->newLine();
        $this->line('AI validation will complete in ~'.($queued * 30).' seconds');
        $this->line('Check results with: php artisan check:employer-validation');

        return 0;
    }

    protected function revokeApproval($validations)
    {
        if (!$this->option('force')) {
            $this->warn('This will REVOKE approval for all legacy accounts.');
            $this->warn('They will NOT be able to post jobs until manually approved.');

            if (!$this->confirm('Continue?')) {
                return 0;
            }
        }

        $this->info('Revoking approval...');

        foreach ($validations as $validation) {
            $validation->update([
                'validation_status' => 'rejected',
                'is_valid' => false,
                'reason' => 'Auto-approval revoked - business permit requires verification',
                'ai_analysis' => array_merge(
                    $validation->ai_analysis ?? [],
                    ['revoked_at' => now()->toDateTimeString(), 'reason' => 'Legacy account approval revoked']
                ),
            ]);

            $this->line("<fg=red>✓</> {$validation->user->email} → Rejected");
        }

        $this->newLine();
        $this->error("All {$validations->count()} legacy accounts are now rejected.");
        $this->line('They must re-upload valid business permits to post jobs.');

        return 0;
    }

    protected function deleteAccounts($validations)
    {
        if (!$this->option('force')) {
            $this->error('⚠️  WARNING: This will PERMANENTLY DELETE all legacy employer accounts!');
            $this->warn('This action CANNOT be undone.');
            $this->warn('All their job postings, applications, and data will be deleted.');

            if (!$this->confirm("Type 'DELETE' to confirm", false)) {
                $this->info('Cancelled.');

                return 0;
            }
        }

        $this->error('Deleting accounts...');
        $deleted = 0;

        foreach ($validations as $validation) {
            $user = $validation->user;
            if (!$user) {
                continue;
            }

            $email = $user->email;

            // Delete validation record
            $validation->delete();

            // Delete user (cascades to job postings, applications, etc.)
            $user->delete();

            $this->line("<fg=red>✗</> Deleted: {$email}");
            ++$deleted;
        }

        $this->newLine();
        $this->error("Permanently deleted {$deleted} employer accounts.");

        return 0;
    }
}
