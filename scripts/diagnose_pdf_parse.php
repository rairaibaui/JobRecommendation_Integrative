<?php
// Simple diagnostic script to test PDF text extraction using Smalot\PdfParser with pdftotext fallback.
// Usage: php scripts/diagnose_pdf_parse.php storage/app/public/resumes/yourfile.pdf

require __DIR__ . '/../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$path = $argv[1] ?? null;
if (!$path) {
    echo "Usage: php scripts/diagnose_pdf_parse.php <path-to-pdf>\n";
    exit(1);
}

$full = realpath($path) ?: $path;
if (!file_exists($full)) {
    echo "File not found: $full\n";
    exit(2);
}

echo "File: $full\n";
echo "Size: " . filesize($full) . " bytes\n";
echo "MIME: " . (@mime_content_type($full) ?: 'unknown') . "\n";

// Try Smalot
try {
    $parser = new Parser();
    $pdf = $parser->parseFile($full);
    $text = trim($pdf->getText() ?: '');
    if ($text !== '') {
        echo "\n== Smalot extracted (first 1000 chars) ==\n";
        echo substr($text, 0, 1000) . "\n";
        echo "\nChars: " . strlen($text) . "\n";
        exit(0);
    }
    echo "\nSmalot extracted empty text.\n";
} catch (\Throwable $e) {
    echo "\nSmalot parse exception: " . $e->getMessage() . "\n";
}

// Try pdftotext fallback
if (function_exists('exec')) {
    // Check availability
    $whichCmd = (stripos(PHP_OS, 'WIN') === 0) ? 'where' : 'which';
    @exec($whichCmd . ' pdftotext 2>&1', $checkOut, $checkRet);
    $has = is_array($checkOut) && count($checkOut) > 0 && $checkRet === 0;
    echo "\npdftotext available: " . ($has ? 'yes' : 'no') . "\n";
    if ($has) {
        $cmd = 'pdftotext -layout -enc UTF-8 ' . escapeshellarg($full) . ' -';
        @exec($cmd, $out, $ret);
        if ($ret === 0 && is_array($out)) {
            $pdftxt = trim(implode("\n", $out));
            if ($pdftxt !== '') {
                echo "\n== pdftotext extracted (first 1000 chars) ==\n";
                echo substr($pdftxt, 0, 1000) . "\n";
                echo "\nChars: " . strlen($pdftxt) . "\n";
                exit(0);
            }
            echo "\npdftotext produced empty output.\n";
        } else {
            echo "\npdftotext command failed (ret=" . var_export($ret, true) . ").\n";
        }
    }
} else {
    echo "\nexec() not available on this PHP installation; cannot try pdftotext.\n";
}

// If we reach here, nothing worked
echo "\nNo text could be extracted from this PDF. It may be a scanned image PDF (needs OCR) or encrypted/corrupt.\n";
exit(3);
