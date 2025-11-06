<?php

namespace App\Console\Commands;

use App\Models\DocumentValidation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteUnverifiedEmployers extends Command
{
    protected $signature = 'employers:delete-unverified {--dry-run : Preview without deleting} {--force : Skip confirmation prompts}';
    protected $description = 'Permanently delete employer accounts without an approved business permit validation';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $employers = User::where('user_type', 'employer')->orderBy('id')->get();
        if ($employers->isEmpty()) {
            $this->info('No employer accounts found.');
            return 0;
        }

        $targets = [];
        foreach ($employers as $employer) {
            $validation = DocumentValidation::where('user_id', $employer->id)
                ->where('document_type', 'business_permit')
                ->orderByDesc('created_at')
                ->first();

            $isApproved = $validation && $validation->validation_status === 'approved' && $validation->is_valid;
            if (!$isApproved) {
                $targets[] = [
                    'user' => $employer,
                    'status' => $validation->validation_status ?? 'none',
                    'has_permit' => !empty($employer->business_permit_path),
                ];
            }
        }

        if (empty($targets)) {
            $this->info('All employer accounts are verified. Nothing to delete.');
            return 0;
        }

        $this->warn('The following unverified employer accounts will be permanently deleted:');
        foreach ($targets as $t) {
            $u = $t['user'];
            $this->line("ID: {$u->id} | {$u->email} | {$u->company_name} | status={$t['status']} | permit=" . ($t['has_permit'] ? 'uploaded' : 'missing'));
        }

        if ($dryRun) {
            $this->info('Dry run complete. No changes made.');
            return 0;
        }

        if (!$force && !$this->confirm('Proceed with PERMANENT deletion of these employer accounts?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $deleted = 0;
        $filesDeleted = 0;

        foreach ($targets as $t) {
            $user = $t['user'];

            // Clean any uploaded files owned by the employer (permit and profile picture)
            $paths = [];
            if (!empty($user->business_permit_path)) {
                $paths[] = $user->business_permit_path;
            }
            if (!empty($user->profile_picture)) {
                $paths[] = $user->profile_picture;
            }

            foreach ($paths as $p) {
                if (Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                    $filesDeleted++;
                }
            }

            // Delete the user (cascades to job postings, applications, validations)
            $user->delete();
            $deleted++;
        }

        $this->newLine();
        $this->error('Deletion complete.');
        $this->line("Employer accounts deleted: {$deleted}");
        $this->line("User files deleted: {$filesDeleted}");

        return 0;
    }
}
