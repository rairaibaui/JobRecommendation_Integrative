<?php
require 'vendor/autoload.php';

$pdo = new PDO('sqlite:database/database.sqlite');
$stmt = $pdo->query('SELECT id, phone_number FROM users');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['id'] . ': ' . $row['phone_number'] . PHP_EOL;
}