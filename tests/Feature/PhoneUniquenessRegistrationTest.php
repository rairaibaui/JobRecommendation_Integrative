<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class PhoneUniquenessRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function job_seeker_registration_rejects_phone_already_in_use_in_different_format()
    {
        // Seed an existing user with a normalized phone
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'phone_number' => '09171234567',
            'user_type' => 'job_seeker',
            'password' => bcrypt('Password1!'),
        ]);

        // Attempt to register a new account using the same number in +63 format
        $response = $this->post('/register', [
            'first_name' => 'New',
            'last_name' => 'Applicant',
            'email' => 'new@applicant.test',
            'phone_number' => '+639171234567',
            'location' => 'Manila',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => 'accepted',
        ]);

        // Should redirect back with a validation error on phone_number
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['phone_number']);
    }
}
