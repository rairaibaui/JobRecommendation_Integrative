<?php
// Usage: php scripts/find_users.php [pattern]
// Example: php scripts/find_users.php duhac

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pattern = $argv[1] ?? 'duhac';

$users = App\Models\User::where('email', 'like', "%{$pattern}%")
    ->orWhere('first_name', 'like', "%{$pattern}%")
    ->orWhere('last_name', 'like', "%{$pattern}%")
    ->limit(50)
    ->get();

if ($users->isEmpty()) {
    echo "No users found matching: {$pattern}\n";
    exit(0);
}

foreach ($users as $u) {
    echo "ID: {$u->id}\n";
    echo "Email: {$u->email}\n";
    echo "Name: {$u->first_name} {$u->last_name}\n";
    echo "Resume file: " . ($u->resume_file ?? 'N/A') . "\n";
    echo "Verification status: " . ($u->resume_verification_status ?? 'N/A') . "\n";
    echo "----\n";
}

return 0;
