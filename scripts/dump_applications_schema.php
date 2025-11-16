<?php
try {
    $db = new PDO('sqlite:'.__DIR__.'/../database/database.sqlite');
    $stmt = $db->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='applications'");
    $res = $stmt ? $stmt->fetchColumn() : null;
    echo $res ?: "<no schema found>";
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage();
}
