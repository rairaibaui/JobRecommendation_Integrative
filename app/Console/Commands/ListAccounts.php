<?php

namespace App\Console\Commands;

use App\Models\DocumentValidation;
use App\Models\User;
use Illuminate\Console\Command;

class ListAccounts extends Command
{
    protected $signature = 'users:list {--type=all : Filter by type: all|employer|job_seeker} {--status= : Employer verification status: verified|unverified}';
    protected $description = 'List user accounts with optional filters and employer verification details';

    public function handle()
    {
        $type = strtolower($this->option('type') ?? 'all');
        $status = $this->option('status');

        $query = User::query();
        if (in_array($type, ['employer', 'job_seeker'], true)) {
            $query->where('user_type', $type);
        }

        $users = $query->orderBy('id')->get();
        if ($users->isEmpty()) {
            $this->info('No users found for the selected filter.');
            return 0;
        }

        $this->info("Accounts (type={$type}" . ($status ? ", status={$status}" : '') . '):');
        $this->line(str_repeat('─', 63));

        $shown = 0;
        $counts = [
            'total' => $users->count(),
            'employer' => 0,
            'job_seeker' => 0,
            'verified' => 0,
            'unverified' => 0,
        ];

        foreach ($users as $u) {
            $counts[$u->user_type] = ($counts[$u->user_type] ?? 0) + 1;

            $row = [
                'id' => $u->id,
                'email' => $u->email,
                'type' => $u->user_type,
            ];

            $include = true;

            if ($u->user_type === 'employer') {
                $validation = DocumentValidation::where('user_id', $u->id)
                    ->where('document_type', 'business_permit')
                    ->orderByDesc('created_at')
                    ->first();
                $isApproved = $validation && $validation->validation_status === 'approved' && $validation->is_valid;
                $row['company'] = $u->company_name ?: '—';
                $row['permit_status'] = $validation->validation_status ?? 'none';
                $row['approved'] = $isApproved ? 'yes' : 'no';
                $row['permit'] = $u->business_permit_path ? 'uploaded' : 'missing';

                if ($isApproved) {
                    $counts['verified']++;
                } else {
                    $counts['unverified']++;
                }

                if ($status === 'verified' && !$isApproved) {
                    $include = false;
                } elseif ($status === 'unverified' && $isApproved) {
                    $include = false;
                }
            }

            if (!$include) {
                continue;
            }

            $this->line("ID: {$row['id']} | {$row['email']} | {$row['type']}");
            if ($u->user_type === 'employer') {
                $this->line("  Company: {$row['company']} | Status: ".strtoupper($row['permit_status'])." | Approved: {$row['approved']} | Permit: {$row['permit']}");
            }
            $this->line('');
            $shown++;
        }

        if ($shown === 0) {
            $this->warn('No accounts matched the filters.');
        }

        $this->line(str_repeat('─', 63));
        $this->info("Total shown: {$shown}");
        $this->info("Users total: {$counts['total']} | Employers: {$counts['employer']} | Job Seekers: {$counts['job_seeker']}");
        if ($type === 'all' || $type === 'employer') {
            $this->info("Employers verified: {$counts['verified']} | unverified: {$counts['unverified']}");
        }

        return 0;
    }
}
