<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo '=== ADMIN ACCOUNTS CHECK ==='.PHP_EOL.PHP_EOL;

$admins = User::where('is_admin', true)->get();

if ($admins->isEmpty()) {
    echo '❌ No admin accounts found!'.PHP_EOL;
    echo PHP_EOL;
    echo 'Creating an admin account for you...'.PHP_EOL;

    // Check if there's an existing user we can promote
    $existingUser = User::where('email', 'alexsandra.duhac2002@gmail.com')->first();

    if ($existingUser) {
        $existingUser->is_admin = true;
        $existingUser->save();
        echo '✅ Promoted alexsandra.duhac2002@gmail.com to admin!'.PHP_EOL;
        echo PHP_EOL;
        echo 'Login Credentials:'.PHP_EOL;
        echo '  Email: alexsandra.duhac2002@gmail.com'.PHP_EOL;
        echo '  Password: (your existing password)'.PHP_EOL;
    } else {
        echo 'No existing account found to promote.'.PHP_EOL;
        echo 'Please create an admin account manually:'.PHP_EOL;
        echo '  1. Register a new account'.PHP_EOL;
        echo '  2. Run this script again to promote it'.PHP_EOL;
    }
} else {
    echo '✅ Found '.$admins->count().' admin account(s):'.PHP_EOL;
    echo str_repeat('=', 60).PHP_EOL;

    foreach ($admins as $admin) {
        echo "Email: {$admin->email}".PHP_EOL;
        echo "Name: {$admin->first_name} {$admin->last_name}".PHP_EOL;
        echo 'Role: '.($admin->role ?? 'N/A').PHP_EOL;
        echo "Created: {$admin->created_at}".PHP_EOL;
        echo str_repeat('-', 60).PHP_EOL;
    }
}

echo PHP_EOL;
echo '📍 Admin Panel URL:'.PHP_EOL;
echo '   http://localhost:8000/admin/verifications'.PHP_EOL;
echo PHP_EOL;
echo '🔐 How to Access:'.PHP_EOL;
echo '   1. Go to: http://localhost:8000/login'.PHP_EOL;
echo '   2. Login with admin email above'.PHP_EOL;
echo '   3. Navigate to: /admin/verifications'.PHP_EOL;
