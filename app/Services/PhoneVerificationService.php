<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PhoneVerificationService
{
    /**
     * Generate and send OTP for phone number verification.
     *
     * @param User $user
     * @param string $newPhoneNumber
     * @return array
     */
    public function sendOTP(User $user, string $newPhoneNumber): array
    {
        // Remove any existing formatting
        $cleanPhone = preg_replace('/\D/', '', $newPhoneNumber);

        // Validate phone number format (11 digits for Philippine numbers)
        if (strlen($cleanPhone) != 11) {
            return [
                'success' => false,
                'message' => 'Invalid phone number format. Please enter 11 digits.',
            ];
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database (expires in 10 minutes)
        DB::table('phone_verifications')->insert([
            'user_id' => $user->id,
            'phone_number' => $cleanPhone,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send OTP via email (since we don't have SMS configured)
        try {
            Mail::send('emails.phone-otp', [
                'user' => $user,
                'otp' => $otp,
                'phone' => $this->formatPhoneNumber($cleanPhone),
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Phone Number Verification Code');
            });

            return [
                'success' => true,
                'message' => 'Verification code sent to your email. Please check your inbox.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.',
            ];
        }
    }

    /**
     * Verify OTP and update phone number.
     *
     * @param User $user
     * @param string $newPhoneNumber
     * @param string $otpCode
     * @return array
     */
    public function verifyOTP(User $user, string $newPhoneNumber, string $otpCode): array
    {
        $cleanPhone = preg_replace('/\D/', '', $newPhoneNumber);

        // Find the most recent OTP for this user and phone number
        $verification = DB::table('phone_verifications')
            ->where('user_id', $user->id)
            ->where('phone_number', $cleanPhone)
            ->where('is_verified', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (!$verification) {
            return [
                'success' => false,
                'message' => 'Verification code expired or not found. Please request a new code.',
            ];
        }

        if ($verification->otp_code !== $otpCode) {
            return [
                'success' => false,
                'message' => 'Invalid verification code. Please try again.',
            ];
        }

        // Mark as verified
        DB::table('phone_verifications')
            ->where('id', $verification->id)
            ->update([
                'is_verified' => true,
                'updated_at' => now(),
            ]);

        // Update user's phone number
        $user->phone_number = $cleanPhone;
        $user->save();

        // Clean up old verification records for this user
        DB::table('phone_verifications')
            ->where('user_id', $user->id)
            ->where('id', '!=', $verification->id)
            ->delete();

        return [
            'success' => true,
            'message' => 'Phone number verified and updated successfully!',
        ];
    }

    /**
     * Format phone number for display.
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber(string $phone): string
    {
        if (strlen($phone) == 11) {
            return substr($phone, 0, 4).' '.substr($phone, 4, 3).' '.substr($phone, 7);
        }

        return $phone;
    }
}
