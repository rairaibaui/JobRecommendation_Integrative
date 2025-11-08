<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('contact_verification_otps')->orderByDesc('created_at')->limit(20)->get();
if ($rows->isEmpty()) {
    echo "No contact_verification_otps found.\n";
    exit;
}

foreach ($rows as $r) {
    echo "id={$r->id} user_id={$r->user_id} type={$r->type} new_value={$r->new_value} verified={$r->verified} expires_at={$r->expires_at} created_at={$r->created_at}\n";
}
