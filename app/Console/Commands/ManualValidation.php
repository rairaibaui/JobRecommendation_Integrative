<?php

namespace App\Console\Commands;

use App\Models\DocumentValidation;
use Illuminate\Console\Command;

class ManualValidation extends Command
{
    protected $signature = 'validate:manual 
                            {action : approve, reject, or approve-all}
                            {--user-id= : Specific user ID to validate}';

    protected $description = 'Manually approve or reject business permit validations';

    public function handle()
    {
        $action = $this->argument('action');
        $userId = $this->option('user-id');

        if ($action === 'approve-all') {
            return $this->approveAll();
        }

        if (!$userId) {
            $this->error('Please provide --user-id for individual actions');

            return 1;
        }

        $validation = DocumentValidation::where('user_id', $userId)
            ->where('document_type', 'business_permit')
            ->first();

        if (!$validation) {
            $this->error("No validation record found for user ID {$userId}");

            return 1;
        }

        if ($action === 'approve') {
            $validation->update([
                'validation_status' => 'approved',
                'is_valid' => true,
                'confidence_score' => 100,
                'reason' => 'Manually approved by administrator',
                'validated_by' => 'admin',
                'validated_at' => now(),
            ]);

            $this->info("✓ Approved business permit for user ID {$userId} ({$validation->user->email})");
        } elseif ($action === 'reject') {
            $validation->update([
                'validation_status' => 'rejected',
                'is_valid' => false,
                'confidence_score' => 0,
                'reason' => 'Manually rejected by administrator - invalid business permit',
                'validated_by' => 'admin',
                'validated_at' => now(),
            ]);

            $this->error("✗ Rejected business permit for user ID {$userId} ({$validation->user->email})");
        } else {
            $this->error('Invalid action. Use: approve, reject, or approve-all');

            return 1;
        }

        return 0;
    }

    protected function approveAll()
    {
        $pending = DocumentValidation::where('validation_status', 'pending_review')
            ->where('document_type', 'business_permit')
            ->with('user')
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending validations found.');

            return 0;
        }

        $this->warn("Found {$pending->count()} pending validations:");
        foreach ($pending as $val) {
            $this->line("  ID: {$val->user_id} - {$val->user->email} - {$val->user->company_name}");
        }

        if (!$this->confirm("\nApprove all {$pending->count()} accounts?")) {
            return 0;
        }

        foreach ($pending as $validation) {
            $validation->update([
                'validation_status' => 'approved',
                'is_valid' => true,
                'confidence_score' => 100,
                'reason' => 'Bulk approved by administrator',
                'validated_by' => 'admin',
                'validated_at' => now(),
            ]);

            $this->line("✓ Approved: {$validation->user->email}");
        }

        $this->newLine();
        $this->info("All {$pending->count()} accounts approved! They can now post jobs.");

        return 0;
    }
}
