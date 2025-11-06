<?php

namespace App\Console\Commands;

use App\Models\DocumentValidation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanUnverifiedPermits extends Command
{
    protected $signature = 'clean:unverified-permits {--dry-run : Preview actions without deleting} {--force : Skip confirmation prompts}';
    protected $description = 'Delete business permit files and validation records for employers without approved validation';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $employers = User::where('user_type', 'employer')->get();
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
                    'latest' => $validation,
                    'all' => DocumentValidation::where('user_id', $employer->id)
                        ->where('document_type', 'business_permit')
                        ->get(),
                ];
            }
        }

        if (empty($targets)) {
            $this->info('Nothing to clean. All employers are verified.');
            return 0;
        }

        // Preview
        $this->warn('The following employer permits/validations will be removed:');
        foreach ($targets as $t) {
            $u = $t['user'];
            $v = $t['latest'];
            $status = $v->validation_status ?? 'none';
            $this->line("ID: {$u->id} | {$u->email} | {$u->company_name}");
            $this->line("  Latest status: {$status} | Permit file: ".($u->business_permit_path ?: 'none'));
            $this->line('');
        }

        if ($dryRun) {
            $this->info('Dry run complete. No changes made.');
            return 0;
        }

        if (!$force && !$this->confirm('Proceed with deleting the above files and validation records?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $deletedFiles = 0;
        $clearedUsers = 0;
        $deletedValidations = 0;

        foreach ($targets as $t) {
            $user = $t['user'];
            $validations = $t['all'];

            // Delete all non-approved validation records for this user
            foreach ($validations as $val) {
                if ($val->validation_status !== 'approved') {
                    $val->delete();
                    $deletedValidations++;
                }
            }

            // Remove uploaded business permit file if any
            if (!empty($user->business_permit_path)) {
                $path = $user->business_permit_path;
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deletedFiles++;
                }
                // Clear user reference
                $user->business_permit_path = null;
                $user->save();
                $clearedUsers++;
            }
        }

        $this->newLine();
        $this->info('Cleanup complete.');
        $this->line("Validation records deleted: {$deletedValidations}");
        $this->line("Permit files deleted: {$deletedFiles}");
        $this->line("Users cleared: {$clearedUsers}");

        return 0;
    }
}
