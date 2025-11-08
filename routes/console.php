<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use App\Models\DocumentValidation;

// Schedule daily check for expired permits (runs at 9:00 AM every day)
Schedule::command('permits:check-expiry')->dailyAt('09:00');
// Schedule revoke outdated verifications daily as a fallback when queue workers are not available
Schedule::command('verification:revoke-outdated')->dailyAt('02:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// List all employer accounts that are not verified to post jobs
Artisan::command('employers:unverified', function () {
    $employers = User::where('user_type', 'employer')->get();

    if ($employers->isEmpty()) {
        $this->warn('No employer accounts found.');
        return 0;
    }

    $unverified = [];

    foreach ($employers as $employer) {
        $validation = DocumentValidation::where('user_id', $employer->id)
            ->where('document_type', 'business_permit')
            ->orderByDesc('created_at')
            ->first();

        $isApproved = $validation && $validation->validation_status === 'approved' && $validation->is_valid;

        if (!$isApproved) {
            $unverified[] = [
                'id' => $employer->id,
                'email' => $employer->email,
                'company' => $employer->company_name ?: '—',
                'status' => $validation->validation_status ?? 'none',
                'is_valid' => $validation->is_valid ?? null,
                'reason' => $validation->reason ?? null,
                'permit' => $employer->business_permit_path ? 'uploaded' : 'missing',
            ];
        }
    }

    if (empty($unverified)) {
        $this->info('All employer accounts are verified.');
        return 0;
    }

    $this->info('Unverified Employer Accounts:');
    $this->line('────────────────────────────────────────────────────────────');
    foreach ($unverified as $row) {
        $this->line("ID: {$row['id']} | {$row['email']} | {$row['company']}");
        $status = strtoupper($row['status']);
        $validTxt = is_null($row['is_valid']) ? 'n/a' : ($row['is_valid'] ? 'true' : 'false');
        $this->line("  Status: {$status} | is_valid: {$validTxt} | Permit: {$row['permit']}");
        if (!empty($row['reason'])) {
            $this->line("  Reason: {$row['reason']}");
        }
        $this->line('');
    }

    $this->line('────────────────────────────────────────────────────────────');
    $this->info('Total employers: '.$employers->count());
    $this->info('Unverified: '.count($unverified));

    return 0;
})->purpose('List employer accounts that are not yet verified');
