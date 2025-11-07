<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [ 'email' => 'admin@jobrecommendation.ph' ],
                [
                    'first_name' => 'System',
                    'last_name' => 'Admin',
                    'email' => 'admin@jobrecommendation.ph',
                    'password' => Hash::make('admin123456'),
                    'is_admin' => true,
                    'user_type' => 'admin',
                    'phone_number' => '09999999999',
                    'date_of_birth' => '1990-01-01',
                    'education_level' => 'N/A',
                    'skills' => 'admin',
                    'years_of_experience' => 0,
                    'location' => 'Mandaluyong',
                ]
        );
    }
}
