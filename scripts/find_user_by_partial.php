<?php
// Usage: php scripts/find_user_by_partial.php partial
if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/find_user_by_partial.php partial\n");
    exit(2);
}
$partial = $argv[1];
$base = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
chdir($base);
require $base . 'vendor/autoload.php';
$app = require_once $base . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::where('email', 'LIKE', "%{$partial}%")->orWhere('first_name','LIKE',"%{$partial}%")->orWhere('last_name','LIKE',"%{$partial}%")->take(20)->get();
$out = [];
foreach ($users as $u) {
    $out[] = ['id'=>$u->id, 'email'=>$u->email, 'name'=>trim($u->first_name.' '.$u->last_name), 'status'=>$u->resume_verification_status];
}
echo json_encode($out, JSON_PRETTY_PRINT) . PHP_EOL;
