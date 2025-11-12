<?php
// Simple environment validator for local/CI use.
// Exits with code 0 if all required variables are present, non-zero otherwise.

$required = [
    'APP_KEY',
    'APP_URL',
    'DB_CONNECTION',
    'DB_DATABASE',
    'FILESYSTEM_DISK',
    // Optional but recommended when AI features enabled
    'OPENAI_API_KEY',
    'OPENAI_MODEL',
    'RESISTANT_API_KEY',
];

$missing = [];
foreach ($required as $k) {
    $v = getenv($k);
    if ($v === false || $v === null || $v === '') {
        $missing[] = $k;
    }
}

if (!empty($missing)) {
    echo "Missing required environment variables:\n" . implode("\n", $missing) . "\n";
    exit(2);
}

echo "All required environment variables are set.\n";
exit(0);
