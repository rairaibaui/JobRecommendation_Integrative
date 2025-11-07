<?php

namespace App\Console\Commands;

use App\Models\DocumentValidation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckExpiredPermits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permits:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired or expiring business permits and send reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired and expiring business permits...');

        // Get all approved permits with expiry dates
        $permits = DocumentValidation::where('validation_status', 'approved')
            ->where('is_valid', true)
            ->whereNotNull('permit_expiry_date')
            ->get();

        if ($permits->isEmpty()) {
            $this->info('No permits with expiry dates found.');

            return 0;
        }

        $expiredCount = 0;
        $expiringCount = 0;
        $today = now()->startOfDay();

        foreach ($permits as $permit) {
            $expiryDate = $permit->permit_expiry_date;
            $daysUntilExpiry = $today->diffInDays($expiryDate, false);

            // Check if permit has expired
            if ($daysUntilExpiry < 0) {
                $this->handleExpiredPermit($permit);
                ++$expiredCount;
            }
            // Check if permit is expiring in 30 days and reminder hasn't been sent
            elseif ($daysUntilExpiry <= 30 && !$permit->expiry_reminder_sent) {
                $this->sendExpiryReminder($permit, (int) $daysUntilExpiry);
                ++$expiringCount;
            }
        }

        $this->info("Processed {$permits->count()} permits:");
        $this->info("- {$expiredCount} expired permits flagged");
        $this->info("- {$expiringCount} expiry reminders sent");

        Log::info("CheckExpiredPermits: Processed {$permits->count()} permits. Expired: {$expiredCount}, Reminders sent: {$expiringCount}");

        return 0;
    }

    /**
     * Handle expired permit by flagging it and notifying the employer.
     */
    protected function handleExpiredPermit(DocumentValidation $permit)
    {
        // Reset validation status to pending_review
        $permit->update([
            'validation_status' => 'pending_review',
            'is_valid' => false,
            'reason' => 'Business permit has expired. Please upload a new valid permit.',
        ]);

        $user = $permit->user;

        // Create notification for employer
        Notification::create([
            'user_id' => $user->id,
            'type' => 'error',
            'title' => 'Business Permit Expired',
            'message' => 'Your business permit has expired. Please upload a new valid permit to continue posting jobs.',
            'read' => false,
        ]);

        // Notify all admins (optional enhancement)
        try {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                // Avoid duplicate admin notifications for the same permit
                $existing = Notification::where('user_id', $admin->id)
                    ->where('type', 'error')
                    ->where('title', 'Business Permit Expired')
                    ->where('data->validation_id', $permit->id)
                    ->first();

                if (!$existing) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'error',
                        'title' => 'Business Permit Expired',
                        'message' => "Employer {$user->company_name} ({$user->email}) has an expired permit (expired on {$permit->permit_expiry_date?->format('M j, Y')}).",
                        'data' => [
                            'validation_id' => $permit->id,
                            'employer_id' => $user->id,
                            'company_name' => $user->company_name,
                            'email' => $user->email,
                            'expiry_date' => optional($permit->permit_expiry_date)->toDateString(),
                        ],
                        'read' => false,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to create admin expired-permit notifications: '.$e->getMessage());
        }

        // Send email notification
        try {
            Mail::send('emails.permit-expired', [
                'user' => $user,
                'permit' => $permit,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Business Permit Expired - Action Required');
            });

            $this->line("  → Expired permit for {$user->company_name} ({$user->email})");
        } catch (\Exception $e) {
            Log::error("Failed to send expiry email to {$user->email}: ".$e->getMessage());
        }
    }

    /**
     * Send expiry reminder to employer.
     */
    protected function sendExpiryReminder(DocumentValidation $permit, int $daysRemaining)
    {
        $user = $permit->user;

        // Create notification for employer
        Notification::create([
            'user_id' => $user->id,
            'type' => 'warning',
            'title' => 'Business Permit Expiring Soon',
            'message' => "Your business permit expires in {$daysRemaining} days. Please prepare to upload a new permit.",
            'read' => false,
        ]);

        // Mark reminder as sent
        $permit->update(['expiry_reminder_sent' => true]);

        // Notify all admins of expiring permit (within 30 days)
        try {
            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                // Avoid duplicates: one notification per admin per validation record
                $existing = Notification::where('user_id', $admin->id)
                    ->where('type', 'warning')
                    ->where('title', 'Business Permit Expiring Soon')
                    ->where('data->validation_id', $permit->id)
                    ->first();

                if (!$existing) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'warning',
                        'title' => 'Business Permit Expiring Soon',
                        'message' => "Employer {$user->company_name} ({$user->email}) permit expires in {$daysRemaining} day(s).",
                        'data' => [
                            'validation_id' => $permit->id,
                            'employer_id' => $user->id,
                            'company_name' => $user->company_name,
                            'email' => $user->email,
                            'expiry_date' => optional($permit->permit_expiry_date)->toDateString(),
                            'days_remaining' => $daysRemaining,
                        ],
                        'read' => false,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to create admin expiring-soon notifications: '.$e->getMessage());
        }

        // Send email notification
        try {
            Mail::send('emails.permit-expiring', [
                'user' => $user,
                'permit' => $permit,
                'daysRemaining' => $daysRemaining,
            ], function ($message) use ($user, $daysRemaining) {
                $message->to($user->email)
                    ->subject("Business Permit Expiring in {$daysRemaining} Days");
            });

            $this->line("  → Reminder sent to {$user->company_name} ({$user->email}) - {$daysRemaining} days remaining");
        } catch (\Exception $e) {
            Log::error("Failed to send reminder email to {$user->email}: ".$e->getMessage());
        }
    }
}
