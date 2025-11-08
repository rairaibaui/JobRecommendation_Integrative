<?php
// Simple DB inspector for local debugging (not committed in production normally)
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Migrations table entries:\n";
$migs = DB::table('migrations')->orderBy('batch', 'desc')->get();
foreach ($migs as $m) {
    echo "- {$m->id} | {$m->migration} | batch: {$m->batch}\n";
}

echo "\nSQLite tables in database file ({$app['config']['database.connections.sqlite.database']}):\n";
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
foreach ($tables as $t) {
    echo "- {$t->name}\n";
}

echo "\nDone.\n";
