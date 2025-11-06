<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Jobs\ValidateBusinessPermitJob;
use App\Models\User;
use App\Services\DocumentValidationService;

$email = $argv[1] ?? null;

if (!$email) {
    echo "Usage: php scripts/revalidate_permit.php <employer_email>" . PHP_EOL;
    exit(1);
}

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found for email: {$email}" . PHP_EOL;
    exit(1);
}

if (!$user->business_permit_path) {
    echo "User has no business_permit_path set." . PHP_EOL;
    exit(1);
}

echo "Revalidating permit for {$email}..." . PHP_EOL;
echo "File: {$user->business_permit_path}" . PHP_EOL;

$metadata = [
    'company_name' => $user->company_name,
    'email' => $user->email,
    'is_personal_email' => (bool) preg_match('/@(gmail|yahoo|outlook|hotmail)\.com$/i', $user->email),
];

// Run job synchronously (bypass queue)
$job = new ValidateBusinessPermitJob($user->id, $user->business_permit_path, $metadata);
$service = app(DocumentValidationService::class);
$job->handle($service);

echo "Done. Check latest validation record for permit_number and status." . PHP_EOL;
