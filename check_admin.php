<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo '=== CHECKING ADMIN ACCOUNT ==='.PHP_EOL.PHP_EOL;

// Check if any admin exists
$admin = User::where('is_admin', 1)->first();

if ($admin) {
    echo '✅ Admin account found!'.PHP_EOL;
    echo 'Email: '.$admin->email.PHP_EOL;
    echo 'Name: '.$admin->first_name.' '.$admin->last_name.PHP_EOL;
    echo PHP_EOL;
    echo 'Resetting password to: admin123456'.PHP_EOL;
    $admin->password = Hash::make('admin123456');
    $admin->save();
    echo '✅ Password reset successfully!'.PHP_EOL;
} else {
    echo '❌ No admin account found in database.'.PHP_EOL;
    echo PHP_EOL;
    echo 'Creating admin account...'.PHP_EOL;

    try {
        $newAdmin = User::create([
            'email' => 'admin@system.com',
            'password' => Hash::make('admin123456'),
            'user_type' => 'employer',
            'is_admin' => true,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'company_name' => 'Job Portal Administration',
            'phone_number' => '09123456789',
            'address' => 'Mandaluyong City Hall',
            'location' => 'Mandaluyong City',
            'email_verified_at' => now(),
        ]);

        echo '✅ Admin account created successfully!'.PHP_EOL;
        $admin = $newAdmin;
    } catch (Exception $e) {
        echo '❌ Failed to create admin: '.$e->getMessage().PHP_EOL;
        exit(1);
    }
}

echo PHP_EOL;
echo '========================================'.PHP_EOL;
echo 'SYSTEM ADMINISTRATOR LOGIN'.PHP_EOL;
echo '========================================'.PHP_EOL;
echo 'Email: '.$admin->email.PHP_EOL;
echo 'Password: admin123456'.PHP_EOL;
echo '========================================'.PHP_EOL;
echo PHP_EOL;
echo '📍 Login at: http://127.0.0.1:8000/login'.PHP_EOL;
echo '📍 Admin Panel: http://127.0.0.1:8000/admin/verifications'.PHP_EOL;
echo PHP_EOL;
