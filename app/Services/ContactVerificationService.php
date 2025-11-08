<?php

namespace App\Services;

use App\Models\ContactVerificationOtp;
use App\Models\User;
use App\Notifications\ContactChangeOtpNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Jobs\AutoUnverifyResumeJob;

class ContactVerificationService
{
    /**
     * Send OTP for contact change (phone) via user's current email.
     */
    public function sendPhoneChangeOtp(User $user, string $newPhone): array
    {
        // Prevent same value
        if (trim($newPhone) === trim($user->phone_number)) {
            return ['success' => false, 'message' => 'The new phone number is the same as your current number.'];
        }

        // Rate limit: same user+new_value within 60s
        $recent = ContactVerificationOtp::where('user_id', $user->id)
            ->where('type', 'phone')
            ->where('new_value', $newPhone)
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return ['success' => false, 'message' => 'Please wait a moment before requesting another code.'];
        }

        // Generate OTP (deterministic in testing)
        if (app()->environment('testing')) {
            $otp = '123456';
        } else {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        $hashed = hash_hmac('sha256', $otp, config('app.key'));
        $expiresAt = now()->addMinutes(10);

        try {
            ContactVerificationOtp::create([
                'user_id' => $user->id,
                'type' => 'phone',
                'new_value' => $newPhone,
                'hashed_otp' => $hashed,
                'expires_at' => $expiresAt,
                'verified' => false,
            ]);

            // Send OTP email to user's current email
            try {
                \Notification::route('mail', $user->email)->notify(new ContactChangeOtpNotification('phone', $newPhone, $otp, $expiresAt));
            } catch (\Throwable $e) {
                Log::error('Failed to send contact change OTP email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                return ['success' => false, 'message' => 'Failed to send code to your email address. Please try again later.'];
            }

            return ['success' => true, 'message' => 'Verification code sent to your email address.'];
        } catch (\Throwable $e) {
            Log::error('Failed to persist contact verification OTP', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Unable to create verification code. Please try again.'];
        }
    }

    /**
     * Verify phone change OTP and update user's phone number.
     */
    public function verifyPhoneChangeOtp(User $user, string $newPhone, string $otp): array
    {
        $record = ContactVerificationOtp::where('user_id', $user->id)
            ->where('type', 'phone')
            ->where('new_value', $newPhone)
            ->where('verified', false)
            ->orderByDesc('created_at')
            ->first();

        if (! $record) {
            return ['success' => false, 'message' => 'No verification code found. Please request a new code.'];
        }

        if ($record->expires_at && now()->greaterThan($record->expires_at)) {
            return ['success' => false, 'message' => 'The verification code has expired. Please request a new code.'];
        }

        $computed = hash_hmac('sha256', $otp, config('app.key'));
        if (! hash_equals($record->hashed_otp, $computed)) {
            return ['success' => false, 'message' => 'Invalid verification code.'];
        }

        try {
            \DB::transaction(function () use ($user, $record, $newPhone) {
                $oldPhone = $user->phone_number;
                // Track whether resume was verified prior to change so we only mark outdated when appropriate
                $wasVerified = (($user->resume_verification_status ?? null) === 'verified');
                $user->phone_number = $newPhone;
                $user->save();

                // Mark OTP as used
                $record->verified = true;
                $record->save();

                // Mark resume as outdated by timestamp only (keep the verified badge visible
                // immediately). We'll record when it became outdated and schedule the
                // auto-unverify job to revoke after the configured minutes if the resume
                // isn't updated.
                if ($wasVerified) {
                    $user->verification_notes = 'Outdated due to phone change ||outdated_due:phone_change';
                    $user->resume_outdated_at = now();
                    $user->save();

                    // Create an in-app notification for the user
                    try {
                        \App\Models\Notification::create([
                            'user_id' => $user->id,
                            'type' => 'warning',
                            'title' => 'Update your resume',
                            'message' => 'Your resume must be updated to match your new contact information.',
                            'read' => false,
                            'data' => ['action' => 'upload_new_resume'],
                        ]);
                    } catch (\Throwable $__e) {
                        // best-effort
                    }

                    // Schedule automatic unverify if user does not update resume within grace period
                    try {
                        $minutes = config('verification.outdated_grace_minutes', 10);
                        AutoUnverifyResumeJob::dispatch($user->id)->delay(now()->addMinutes($minutes));
                    } catch (\Throwable $__dispatchEx) {
                        Log::warning('Failed to dispatch AutoUnverifyResumeJob', ['user_id' => $user->id, 'error' => $__dispatchEx->getMessage()]);
                    }
                }

                // (Notifications and scheduling for outdated resume are handled above when appropriate)
            });

            // No additional mail sent here (OTP email already confirms). If desired, add a confirmation mail.

            // Log success to aid debugging when UI reports errors but DB was updated
            try {
                Log::info('Phone change verified for user', ['user_id' => $user->id, 'new_phone' => $newPhone]);
            } catch (\Throwable $__logEx) {
                // ignore logging failures
            }

            return ['success' => true, 'message' => 'Phone updated successfully. Please update your resume to reflect your new contact details.'];
        } catch (\Throwable $e) {
            Log::error('Failed to update phone during OTP verification', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to update phone. Please try again.'];
        }
    }
}
