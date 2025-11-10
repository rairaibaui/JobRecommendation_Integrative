<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo '=== FIXING ADMIN USER TYPE ==='.PHP_EOL;

// Update all admin accounts to have correct user_type
$updated = User::where('is_admin', true)->update(['user_type' => 'admin']);

echo "✅ Updated {$updated} admin account(s) to user_type = 'admin'".PHP_EOL;