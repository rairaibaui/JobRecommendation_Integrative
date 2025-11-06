<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin')->first();

if ($user) {
    echo 'Admin account check:'.PHP_EOL;
    echo "Email: {$user->email}".PHP_EOL;
    echo 'is_admin: '.($user->is_admin ? 'TRUE' : 'FALSE').PHP_EOL;
    echo "user_type: {$user->user_type}".PHP_EOL;
    echo "role: {$user->role}".PHP_EOL;
} else {
    echo 'Admin account not found!'.PHP_EOL;
}
