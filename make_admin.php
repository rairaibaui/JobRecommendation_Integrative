<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::first();

if ($user) {
    $user->is_admin = true;
    $user->save();
    
    echo "✅ Admin access granted!\n";
    echo "Email: {$user->email}\n";
    echo "Name: {$user->first_name} {$user->last_name}\n";
    echo "\nYou can now access: http://127.0.0.1:8000/admin\n";
} else {
    echo "❌ No users found.\n";
}
