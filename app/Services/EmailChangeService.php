<?php

namespace App\Services;

use App\Models\EmailChangeOtp;
use App\Models\User;
use App\Notifications\EmailChangeOtpNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class EmailChangeService
{
    /**
     * Generate and send a 6-digit OTP to a new email address.
     * Rate-limits repeated requests to avoid abuse.
     * Returns array with success/message.
     */
    public function sendOtp(User $user, string $newEmail): array
    {
        // Prevent requesting OTP to the same current email
        if (Str::lower($newEmail) === Str::lower($user->email)) {
            return ['success' => false, 'message' => 'The new email is the same as your current email.'];
        }

        // Ensure email is not used by another user
        if (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            return ['success' => false, 'message' => 'That email is already in use.'];
        }

        // Rate limit: if an OTP for this user+email was created within last 60 seconds, deny
        $recent = EmailChangeOtp::where('user_id', $user->id)
            ->where('new_email', $newEmail)
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return ['success' => false, 'message' => 'Please wait a moment before requesting another code.'];
        }

        // Generate a 6-digit OTP. Use a fixed code in testing environment for deterministic tests.
        if (app()->environment('testing')) {
            $otp = '123456';
        } else {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        // Hash the OTP using HMAC with app key for secure storage
        $hashed = hash_hmac('sha256', $otp, config('app.key'));

        $expiresAt = now()->addMinutes(10);

        try {
            EmailChangeOtp::create([
                'user_id' => $user->id,
                'new_email' => $newEmail,
                'hashed_otp' => $hashed,
                'expires_at' => $expiresAt,
                'verified' => false,
            ]);

            // Send OTP email to the requested new address
            try {
                \Notification::route('mail', $newEmail)->notify(new EmailChangeOtpNotification($otp, $expiresAt));
            } catch (\Throwable $e) {
                Log::error('Failed to send email change OTP', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                // don't leak internal mail error to user; but still indicate failure
                return ['success' => false, 'message' => 'Failed to send code to the provided email. Please try again later.'];
            }

            return ['success' => true, 'message' => 'Verification code sent to the new email address.'];
        } catch (\Throwable $e) {
            Log::error('Failed to persist email change OTP', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Unable to create email verification code. Please try again.'];
        }
    }

    /**
     * Verify OTP and, on success, update the user's email.
     */
    public function verifyOtp(User $user, string $newEmail, string $otp): array
    {
        // Find the latest unverified OTP for this user and email
        $record = EmailChangeOtp::where('user_id', $user->id)
            ->where('new_email', $newEmail)
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
        // Use hash_equals to avoid timing attacks
        if (! hash_equals($record->hashed_otp, $computed)) {
            return ['success' => false, 'message' => 'Invalid verification code.'];
        }

        // Ensure the email is still unique (race condition safe)
        if (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            return ['success' => false, 'message' => 'That email is already in use.'];
        }

        try {
            \DB::transaction(function () use ($user, $record, $newEmail) {
                // Update user's email and mark OTP record verified
                $oldEmail = $user->email;
                $user->email = $newEmail;
                // When email is changed via OTP, mark email as verified (so user won't need to re-verify)
                $user->email_verified_at = now();
                $user->save();

                $record->verified = true;
                $record->save();

                // In-app notification for success
                try {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'success',
                        'title' => 'Email Updated',
                        'message' => 'Your account email was changed successfully.',
                        'read' => false,
                        'data' => ['old_email' => $oldEmail, 'new_email' => $newEmail],
                    ]);
                } catch (\Throwable $e) {
                    // best effort
                }
            });

            // No outbound notification to old/new email beyond the OTP sending.

            return ['success' => true, 'message' => 'Email updated successfully.'];
        } catch (\Throwable $e) {
            Log::error('Failed to update user email during OTP verification', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to update email. Please try again.'];
        }
    }
}
