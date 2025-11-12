<?php
require __DIR__ . '/check_doc_type.php';
$ocr = "BUS1NESS LOCAT10NAL C L E A R A N C E\nMARG1E STORE\nMARGARITA P MONDER0\nPH. 3 L0T 12 BLK E. BLK 40 BRGY ADD1T10N H1LLS\nMANDALUY0NG C1TY\nSARI-SAR1 STORE";
$r = \DocChecker\decideStatus($ocr, true, true, 0.8);
print_r($r);
