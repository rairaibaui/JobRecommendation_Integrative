<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo '=== CREATE ADMIN ACCOUNT ==='.PHP_EOL.PHP_EOL;

// Check if admin account already exists
$existingAdmin = User::where('email', 'admin')->first();

if ($existingAdmin) {
    echo 'Admin account already exists. Updating password...'.PHP_EOL;
    $existingAdmin->password = Hash::make('admin123456');
    $existingAdmin->is_admin = true;
    $existingAdmin->save();
    echo '✅ Admin password updated!'.PHP_EOL;
} else {
    echo 'Creating new admin account...'.PHP_EOL;

    $admin = User::create([
        'email' => 'admin',
        'password' => Hash::make('admin123456'),
        'user_type' => 'employer',
        'is_admin' => true,
        'first_name' => 'Admin',
        'last_name' => 'User',
        'company_name' => 'System Administrator',
        'phone_number' => '0000000000',
        'address' => 'Admin Office',
        'location' => 'Mandaluyong City',
        'email_verified_at' => now(),
    ]);

    echo '✅ Admin account created successfully!'.PHP_EOL;
}

echo PHP_EOL;
echo '========================================'.PHP_EOL;
echo 'Admin Login Credentials:'.PHP_EOL;
echo '========================================'.PHP_EOL;
echo 'Email/Username: admin'.PHP_EOL;
echo 'Password: admin123456'.PHP_EOL;
echo '========================================'.PHP_EOL;
echo PHP_EOL;
echo '📍 Login URL: http://localhost:8000/login'.PHP_EOL;
echo '📍 Admin Panel: http://localhost:8000/admin/verifications'.PHP_EOL;
echo PHP_EOL;
echo '🔐 Steps to Access:'.PHP_EOL;
echo '  1. Go to http://localhost:8000/login'.PHP_EOL;
echo '  2. Enter email: admin'.PHP_EOL;
echo '  3. Enter password: admin123456'.PHP_EOL;
echo '  4. Navigate to: /admin/verifications'.PHP_EOL;
echo PHP_EOL;
