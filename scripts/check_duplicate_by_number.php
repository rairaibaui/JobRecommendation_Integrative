<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DocumentValidation;

echo "=== DUPLICATE CHECK BY PERMIT NUMBER ===" . PHP_EOL . PHP_EOL;

$validations = DocumentValidation::where('document_type', 'business_permit')
    ->whereNotNull('permit_number')
    ->orderBy('permit_number')
    ->get();

if ($validations->isEmpty()) {
    echo "No validations with permit_number found." . PHP_EOL;
    exit(0);
}

$groups = $validations->groupBy('permit_number');

foreach ($groups as $permitNumber => $records) {
    $count = $records->count();
    echo str_repeat('=', 60) . PHP_EOL;
    echo "Permit Number: {$permitNumber}" . PHP_EOL;
    echo "Total Records: {$count}" . PHP_EOL;

    foreach ($records as $rec) {
        $email = optional($rec->user)->email;
        echo " - ID {$rec->id} | User: {$email} | Status: {$rec->validation_status} | Approved: "
            . ($rec->validation_status === 'approved' ? 'YES' : 'NO') . PHP_EOL;
    }
}

echo PHP_EOL . "Done." . PHP_EOL;
