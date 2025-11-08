<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use App\Models\User;

class EmailVerificationExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_link_valid_within_expiry()
    {
        Config::set('auth.verification.expire', 5);

        $user = User::factory()->create([
            'email' => 'test-valid@example.com',
            'email_verified_at' => null,
        ]);

        $signedUrl = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(config('auth.verification.expire')), ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]);

        $this->actingAs($user)
             ->get($signedUrl)
             ->assertRedirect(route('dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_link_expires_after_configured_minutes()
    {
        Config::set('auth.verification.expire', 5);

        $user = User::factory()->create([
            'email' => 'test-expired@example.com',
            'email_verified_at' => null,
        ]);

        $signedUrl = URL::temporarySignedRoute('verification.verify', Carbon::now()->addMinutes(config('auth.verification.expire')), ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]);

    // Advance time beyond expiry
    Carbon::setTestNow(Carbon::now()->addMinutes(config('auth.verification.expire') + 1));

    // Request the verification link while NOT authenticated. The route intentionally
    // bypasses signature checks when the user is already authenticated, so to assert
    // expiry behavior we must be unauthenticated.
    $response = $this->get($signedUrl);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'Invalid or expired verification link.');

    $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
