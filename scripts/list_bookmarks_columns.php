<?php
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) { echo "no_db\n"; exit; }
$pdo = new PDO('sqlite:' . $db);
$cols = $pdo->query("PRAGMA table_info('bookmarks')")->fetchAll(PDO::FETCH_ASSOC);
if (!$cols) { echo "no_table\n"; exit; }
foreach ($cols as $c) {
    echo $c['cid'] . '|' . $c['name'] . '|' . $c['type'] . '|' . ($c['notnull'] ? 1 : 0) . '|' . $c['dflt_value'] . PHP_EOL;
}
