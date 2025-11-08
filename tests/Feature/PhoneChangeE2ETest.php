<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Services\ResumeVerificationService;

class PhoneChangeE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_change_phone_and_reupload_resume_triggers_verification()
    {
        Storage::fake('public');

        // Create user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'phone_number' => '09171234567',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        // Send phone change OTP
        $resp = $this->postJson('/profile/change-phone', ['new_phone' => '09179876543']);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        // Verify with deterministic test OTP
        $resp2 = $this->postJson('/profile/verify-phone-change-otp', ['new_phone' => '09179876543', 'otp_code' => '123456']);
        $resp2->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'phone_number' => '09179876543', 'resume_verification_status' => 'outdated']);

        // Mock ResumeVerificationService so verify() returns a deterministic 'verified' result
        $this->mock(ResumeVerificationService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturn([
                'status' => 'verified',
                'score' => 95,
                'flags' => [],
                'notes' => 'All fields matched in test mock',
                'verified_at' => now(),
            ]);
        });

        // Upload a new resume file
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'resume_file' => $file,
        ];

    $resp3 = $this->post('/profile/update', $data);
    // ProfileController redirects back on success (non-AJAX), expect 302
    $resp3->assertStatus(302);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'resume_verification_status' => 'verified']);
    }
}
