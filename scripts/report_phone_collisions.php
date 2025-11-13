<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

function normalizePhoneForReport($raw) {
    if (empty($raw)) return null;
    $s = preg_replace('/[^0-9]/', '', $raw);
    if ($s === '') return null;
    if (strpos($s, '63') === 0 && strlen($s) >= 11) {
        $s = '0' . substr($s, 2);
    }
    if (strlen($s) === 10 && strpos($s, '9') === 0) {
        $s = '0' . $s;
    }
    if (strlen($s) > 11) {
        $s = substr($s, -11);
    }
    return $s;
}

$map = [];
foreach (User::whereNotNull('phone_number')->cursor() as $u) {
    $n = normalizePhoneForReport($u->phone_number);
    if (!isset($map[$n])) $map[$n] = [];
    $map[$n][] = ['id' => $u->id, 'email' => $u->email, 'stored' => $u->phone_number];
}

$hasCollisions = false;
foreach ($map as $n => $users) {
    if (empty($n)) continue;
    if (count($users) > 1) {
        $hasCollisions = true;
        echo "Collision for normalized phone: $n" . PHP_EOL;
        foreach ($users as $info) {
            echo "  - id={$info['id']} email={$info['email']} stored='{$info['stored']}'" . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

if (!$hasCollisions) {
    echo "No phone collisions found after normalization.\n";
}
