<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class EmailChangeOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_and_verify_email_change_otp_success()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user);

        $newEmail = 'new@example.com';

        $resp = $this->postJson(route('profile.changeEmail'), ['new_email' => $newEmail]);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        // Now verify using deterministic OTP (testing env uses 123456)
        $verify = $this->postJson(route('profile.verifyEmailOTP'), ['new_email' => $newEmail, 'otp_code' => '123456']);
        $verify->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $newEmail]);
    }

    public function test_otp_expires_and_cannot_be_used()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'old2@example.com']);
        $this->actingAs($user);

        $newEmail = 'expired@example.com';

        $this->postJson(route('profile.changeEmail'), ['new_email' => $newEmail])->assertStatus(200);

        // Manually expire the OTP record
        $this->artisan('migrate'); // ensure migrations run (RefreshDatabase already did)

        $otp = \App\Models\EmailChangeOtp::where('user_id', $user->id)->where('new_email', $newEmail)->first();
        $this->assertNotNull($otp);
        $otp->expires_at = now()->subMinutes(1);
        $otp->save();

        $verify = $this->postJson(route('profile.verifyEmailOTP'), ['new_email' => $newEmail, 'otp_code' => '123456']);
        $verify->assertStatus(400)->assertJson(['success' => false]);
    }
}
