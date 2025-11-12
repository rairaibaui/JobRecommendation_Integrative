<?php
declare(strict_types=1);

namespace DocChecker;

use function similar_text;

function normalizeText(string $text): string
{
    $t = mb_strtolower($text, 'UTF-8');
    if (function_exists('iconv')) {
        $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t);
        if ($tmp !== false) $t = $tmp;
    }
    $confusions = [
        '/\\bi\\b/u' => '1',
        '/\\b0\\b/u' => '0',
        '/\\bo\\b/u' => '0',
        '/[\\x{2018}\\x{2019}\\x{201C}\\x{201D}]/u' => "'",
        '/[^\\p{L}\\p{N}\\s]/u' => ' ',
    ];
    foreach ($confusions as $pat => $rep) {
        $t = preg_replace($pat, $rep, $t);
    }
    $t = preg_replace(['/\\bl(?=\\d)/u', '/(?<=\\d)l/u', '/(?<=\\d)O/u', '/\\bO\\b/u'], ['1', '1', '0', '0'], $t);
    $t = preg_replace('/\\s+/u', ' ', $t);
    return trim($t);
}

function fuzzyMatchKeyword(string $ocr, string $keyword): array
{
    $ocrNorm = normalizeText($ocr);
    $kwNorm = normalizeText($keyword);
    if ($kwNorm === '') return [0.0, ''];
    if (strpos($ocrNorm, $kwNorm) !== false) return [1.0, $kwNorm];

    $ocrWords = preg_split('/\\s+/u', $ocrNorm, -1, PREG_SPLIT_NO_EMPTY);
    $kwWords = preg_split('/\\s+/u', $kwNorm, -1, PREG_SPLIT_NO_EMPTY);
    $k = count($kwWords);
    $total = count($ocrWords);
    if ($total === 0 || $k === 0) return [0.0, ''];

    $best = 0.0;
    $bestMatch = '';

    for ($i = 0; $i + $k - 1 < $total; $i++) {
        $window = array_slice($ocrWords, $i, $k);
        $sum = 0.0; $count = 0;
        for ($j = 0; $j < $k; $j++) {
            $a = $kwWords[$j];
            $b = $window[$j] ?? '';
            if ($a === '' || $b === '') { $score = 0.0; }
            else { similar_text($a, $b, $p); $score = ($p / 100.0); }
            $sum += $score; $count++;
        }
        if ($count === 0) continue;
        $avg = $sum / $count;
        $phraseA = implode(' ', $kwWords);
        $phraseB = implode(' ', $window);
        similar_text($phraseA, $phraseB, $phraseP);
        $phraseScore = $phraseP / 100.0;
        $score = ($avg * 0.7) + ($phraseScore * 0.3);
        if ($score > $best) { $best = $score; $bestMatch = implode(' ', $window); }
        if ($best >= 1.0) break;
    }

    return [$best, $bestMatch];
}

function detectDocumentType(string $ocr, float $threshold = 0.85): array
{
    $lists = [
        'BARANGAY_CLEARANCE' => [
            'barangay clearance','barangay locational clearance','business locational clearance','locational clearance','business locational clearance','locational permit'
        ],
        'BUSINESS_REGISTRATION' => [
            'certificate of business name registration','business name registration','certificate of business name','dti','department of trade and industry','certificate of registration'
        ],
        'MAYORS_PERMIT' => [
            'mayor s permit','mayor\'s permit','mayors permit','business permit','permit to operate'
        ],
    ];

    $bestType = 'UNKNOWN';
    $bestScore = 0.0;
    $bestKeyword = '';
    $candidates = [];

    foreach ($lists as $type => $keywords) {
        foreach ($keywords as $kw) {
            [$score, $match] = fuzzyMatchKeyword($ocr, $kw);
            $candidates[] = ['type' => $type, 'keyword' => $kw, 'score' => $score, 'match' => $match];
            if ($score > $bestScore) { $bestScore = $score; $bestType = $type; $bestKeyword = $kw; }
            if ($bestScore >= 1.0) break 2;
        }
    }

    usort($candidates, function($a, $b) { return $b['score'] <=> $a['score']; });

    $ocrNorm = normalizeText($ocr);
    foreach ($lists as $type => $keywords) {
        foreach ($keywords as $kw) {
            if (strpos($ocrNorm, normalizeText($kw)) !== false) {
                array_unshift($candidates, ['type'=>$type,'keyword'=>$kw,'score'=>1.0,'match'=>$kw]);
                return [$type, $kw, 1.0, $candidates];
            }
        }
    }

    return [$bestType, $bestKeyword, $bestScore, $candidates];
}

function decideStatus(string $ocr, bool $emailVerified, bool $hasSignature, float $threshold = 0.85): array
{
    $ocrNorm = normalizeText($ocr);
    $ocrLen = mb_strlen(preg_replace('/\\s+/u', '', $ocrNorm), 'UTF-8');

    [$docType, $matchedKeyword, $matchScore, $candidates] = detectDocumentType($ocr, $threshold);
    // consider a keyword present only if the fuzzy match is above a small floor
    $baseHasKeyword = ($docType !== 'UNKNOWN') && ($matchScore > 0.18);

    if (! $baseHasKeyword || $ocrLen < 30) {
        return [
            'decision' => 'BLOCKED_BY_AI',
            'reason' => ! $baseHasKeyword ? 'no_keywords' : 'ocr_too_short',
            'code' => 3,
            'docType' => $docType,
            'ocr_length' => $ocrLen,
            'matched_keyword' => $matchedKeyword,
            'match_score' => $matchScore,
            'candidates' => array_slice($candidates, 0, 3),
        ];
    }

    $keywordScore = $baseHasKeyword ? 0.5 : 0.0;
    if ($matchScore > $threshold) $keywordScore += 0.25;
    $signatureBoost = $hasSignature ? 0.15 : 0.0;
    $finalScore = $keywordScore + $signatureBoost;

    if ($finalScore >= 0.7 && $emailVerified && $hasSignature) {
        return [
            'decision' => 'AUTO_APPROVED',
            'reason' => 'auto_criteria_met',
            'code' => 0,
            'docType' => $docType,
            'ocr_length' => $ocrLen,
            'matched_keyword' => $matchedKeyword,
            'match_score' => $matchScore,
            'final_score' => $finalScore,
            'candidates' => array_slice($candidates, 0, 3),
        ];
    }

    return [
        'decision' => 'REVIEW_BY_ADMIN',
        'reason' => 'needs_manual_review',
        'code' => 2,
        'docType' => $docType,
        'ocr_length' => $ocrLen,
        'matched_keyword' => $matchedKeyword,
        'match_score' => $matchScore,
        'final_score' => $finalScore,
        'candidates' => array_slice($candidates, 0, 3),
    ];
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === realpath(__FILE__)) {
    $longopts = [
        'file:', 'email-verified', 'email', 'has-signature', 'sig', 'output:', 'threshold:', 'dry-run'
    ];
    $opts = getopt('f:', $longopts);
    $file = $opts['f'] ?? ($opts['file'] ?? null);
    $emailFlag = isset($opts['email-verified']) || isset($opts['email']);
    $sigFlag = isset($opts['has-signature']) || isset($opts['sig']);
    $output = $opts['output'] ?? 'text';
    $threshold = isset($opts['threshold']) ? floatval($opts['threshold']) : 0.85;
    $dryRun = isset($opts['dry-run']);

    $posArg = null;
    foreach ($_SERVER['argv'] as $i => $v) {
        if ($i === 0) continue;
        if (strpos($v, '-') === 0) continue;
        $posArg = $v; break;
    }

    $ocr = '';
    if ($file) {
        if (!file_exists($file)) { fwrite(STDERR, "File not found: $file\n"); exit(1); }
        $ocr = file_get_contents($file);
    } elseif ($posArg === null || $posArg === '-') {
        $ocr = stream_get_contents(STDIN);
    } else {
        $ocr = $posArg;
    }

    try {
        $result = decideStatus($ocr, $emailFlag, $sigFlag, $threshold);
    } catch (\Throwable $e) {
        fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
        exit(1);
    }

    $json = [
        'decision' => $result['decision'],
        'reason' => $result['reason'] ?? null,
        'docType' => $result['docType'] ?? null,
        'ocr_length' => $result['ocr_length'] ?? null,
        'matched_keyword' => $result['matched_keyword'] ?? null,
        'match_score' => $result['match_score'] ?? null,
    ];

    if ($output === 'json') {
        echo json_encode($json, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    } else {
        echo "Decision: " . $result['decision'] . PHP_EOL;
        echo "Reason: " . ($result['reason'] ?? '') . PHP_EOL;
        echo "DocType: " . ($result['docType'] ?? '') . PHP_EOL;
        echo "OCR length: " . ($result['ocr_length'] ?? '') . PHP_EOL;
        echo "Matched Keyword: " . ($result['matched_keyword'] ?? '') . PHP_EOL;
        echo "Match Score: " . ($result['match_score'] ?? '') . PHP_EOL;
        if (isset($result['final_score'])) echo "Final Score: " . $result['final_score'] . PHP_EOL;
    }

    $cands = $result['candidates'] ?? [];
    if ($result['code'] === 3) {
        fwrite(STDERR, "Blocked: top candidates:\n");
        foreach ($cands as $c) {
            fwrite(STDERR, sprintf(" - %s (%s): %.3f\n", $c['keyword'], $c['type'], $c['score']));
        }
    }

    if ($dryRun) {
        fwrite(STDERR, "DRY-RUN diagnostics:\n");
        fwrite(STDERR, "FinalScore: " . ($result['final_score'] ?? 'N/A') . "\n");
        fwrite(STDERR, "Top candidates:\n");
        foreach ($cands as $c) {
            fwrite(STDERR, sprintf(" - %s (%s): %.3f\n", $c['keyword'], $c['type'], $c['score']));
        }
        exit(0);
    }

    exit(intval($result['code'] ?? 1));
}


