<?php

namespace App\Console\Commands;

use App\Models\DocumentValidation;
use App\Models\User;
use Illuminate\Console\Command;

class CheckEmployerValidation extends Command
{
    protected $signature = 'check:employer-validation {--fix : Create pending validation records for employers without validation}';
    protected $description = 'Check employer accounts for business permit validation status';

    public function handle()
    {
        $this->info('Checking employer validation status...');
        $this->newLine();

        $employers = User::where('user_type', 'employer')->get();

        if ($employers->isEmpty()) {
            $this->warn('No employer accounts found.');

            return 0;
        }

        $this->info("Found {$employers->count()} employer account(s):");
        $this->newLine();

        $employersWithoutValidation = [];

        foreach ($employers as $employer) {
            $validation = DocumentValidation::where('user_id', $employer->id)
                ->where('document_type', 'business_permit')
                ->first();

            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("Employer ID: {$employer->id}");
            $this->line("Email: {$employer->email}");
            $this->line('Company: '.($employer->company_name ?: 'Not set'));
            $this->line('Phone: '.($employer->phone_number ?: 'Not set'));
            $this->line('Business Permit Path: '.($employer->business_permit_path ?: 'Not uploaded'));

            if ($validation) {
                $statusColor = match ($validation->validation_status) {
                    'approved' => 'green',
                    'rejected' => 'red',
                    'pending_review' => 'yellow',
                    default => 'white'
                };

                $this->line("<fg={$statusColor}>Validation Status: {$validation->validation_status}</>");
                $this->line('Valid: '.($validation->is_valid ? 'YES' : 'NO'));
                $this->line("Confidence: {$validation->confidence_score}%");
                $this->line("Validated By: {$validation->validated_by}");

                if ($validation->reason) {
                    $this->line("Reason: {$validation->reason}");
                }

                // Check if can post jobs
                if ($validation->is_valid && $employer->company_name && $employer->phone_number) {
                    $this->line('<fg=green>✓ Can post jobs</>');
                } elseif (!$validation->is_valid) {
                    $this->line('<fg=red>✗ Cannot post jobs - Business permit not validated</>');
                } elseif (!$employer->company_name || !$employer->phone_number) {
                    $this->line('<fg=yellow>⚠ Cannot post jobs - Missing company details</>');
                }
            } else {
                $this->line('<fg=yellow>⚠ No validation record found</>');

                if ($employer->company_name && $employer->phone_number) {
                    $this->line('<fg=green>✓ Can post jobs (validation not required for existing accounts)</>');
                } else {
                    $this->line('<fg=yellow>⚠ Cannot post jobs - Missing company details</>');
                }

                $employersWithoutValidation[] = $employer;
            }

            $this->newLine();
        }

        // Summary
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('Summary:');
        $this->line("Total Employers: {$employers->count()}");
        $this->line('Without Validation: '.count($employersWithoutValidation));
        $this->newLine();

        // Offer to fix
        if (!empty($employersWithoutValidation) && $this->option('fix')) {
            $this->info('Creating pending validation records for employers without validation...');

            foreach ($employersWithoutValidation as $employer) {
                if ($employer->business_permit_path) {
                    DocumentValidation::create([
                        'user_id' => $employer->id,
                        'document_type' => 'business_permit',
                        'file_path' => $employer->business_permit_path,
                        'is_valid' => true, // Grandfather in existing employers
                        'confidence_score' => 100,
                        'validation_status' => 'approved',
                        'reason' => 'Legacy account - automatically approved (created before AI validation)',
                        'ai_analysis' => ['note' => 'Grandfathered existing employer account'],
                        'validated_by' => 'system',
                        'validated_at' => now(),
                    ]);

                    $this->line("<fg=green>✓ Created approved validation for {$employer->email}</>");
                } else {
                    DocumentValidation::create([
                        'user_id' => $employer->id,
                        'document_type' => 'business_permit',
                        'file_path' => null,
                        'is_valid' => true, // Allow posting even without permit for legacy accounts
                        'confidence_score' => 100,
                        'validation_status' => 'approved',
                        'reason' => 'Legacy account - no business permit on file (created before AI validation)',
                        'ai_analysis' => ['note' => 'Grandfathered existing employer account without permit'],
                        'validated_by' => 'system',
                        'validated_at' => now(),
                    ]);

                    $this->line("<fg=yellow>⚠ Created approved validation for {$employer->email} (no permit on file)</>");
                }
            }

            $this->newLine();
            $this->info('All existing employers can now post jobs!');
        } elseif (!empty($employersWithoutValidation)) {
            $this->newLine();
            $this->warn('To automatically approve these employers, run:');
            $this->line('php artisan check:employer-validation --fix');
        }

        return 0;
    }
}
