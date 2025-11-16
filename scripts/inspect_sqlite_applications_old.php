<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$stmt = $pdo->query("SELECT type, name, sql FROM sqlite_master WHERE sql LIKE '%applications_old%' OR name LIKE '%applications_old%';");
$rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
