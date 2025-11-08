<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class PhoneChangeOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_and_verify_phone_change_otp_success()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'owner@example.com', 'phone_number' => '09171234567']);
        $this->actingAs($user);

        $newPhone = '09179998877';

    $resp = $this->postJson('/profile/change-phone', ['new_phone' => $newPhone]);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        // Verify with deterministic OTP (123456 in testing)
    $verify = $this->postJson('/profile/verify-phone-change-otp', ['new_phone' => $newPhone, 'otp_code' => '123456']);
        $verify->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'phone_number' => $newPhone]);

        // Resume should be marked outdated
        $this->assertDatabaseHas('users', ['id' => $user->id, 'resume_verification_status' => 'outdated']);
    }

    public function test_phone_change_otp_expires()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'owner2@example.com', 'phone_number' => '09170001111']);
        $this->actingAs($user);

        $newPhone = '09173334444';
    $this->postJson('/profile/change-phone', ['new_phone' => $newPhone])->assertStatus(200);

        $otp = \App\Models\ContactVerificationOtp::where('user_id', $user->id)->where('new_value', $newPhone)->first();
        $this->assertNotNull($otp);
        $otp->expires_at = now()->subMinutes(1);
        $otp->save();

        $verify = $this->postJson(route('profile.verifyPhoneChangeOTP'), ['new_phone' => $newPhone, 'otp_code' => '123456']);
        $verify->assertStatus(400)->assertJson(['success' => false]);
    }
}
