<?php
require 'vendor/autoload.php';

$pdo = new PDO('sqlite:database/database.sqlite');

// Find the duplicate phone number
$stmt = $pdo->query("SELECT phone_number, COUNT(*) as count FROM users WHERE phone_number IS NOT NULL GROUP BY phone_number HAVING count > 1");
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($duplicates)) {
    echo "No duplicate phone numbers found.\n";
    exit;
}

echo "Found duplicate phone numbers:\n";
foreach ($duplicates as $dup) {
    echo "Phone: {$dup['phone_number']} appears {$dup['count']} times\n";

    // Get all users with this phone number
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE phone_number = ?");
    $stmt->execute([$dup['phone_number']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Users with this phone:\n";
    foreach ($users as $user) {
        echo "  ID: {$user['id']}, Email: {$user['email']}\n";
    }

    // Keep the first user, null out phone for others
    $first = true;
    foreach ($users as $user) {
        if ($first) {
            echo "  Keeping phone for user ID: {$user['id']}\n";
            $first = false;
        } else {
            echo "  Removing phone from user ID: {$user['id']}\n";
            $pdo->prepare("UPDATE users SET phone_number = NULL WHERE id = ?")->execute([$user['id']]);
        }
    }
    echo "\n";
}

echo "Duplicate phone numbers fixed.\n";