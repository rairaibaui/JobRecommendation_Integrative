<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$u = DB::table('users')->where('id', 45)->first();
if (!$u) {
    echo "user not found\n";
    exit;
}

echo "user id={$u->id} phone=" . ($u->phone_number ?? 'NULL') . "\n";
