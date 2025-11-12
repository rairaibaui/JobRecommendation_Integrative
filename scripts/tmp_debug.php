<?php
require __DIR__ . '/check_doc_type.php';
$r = \DocChecker\decideStatus(str_repeat('Lorem ipsum dolor sit amet ',10), true, true);
print_r($r);
