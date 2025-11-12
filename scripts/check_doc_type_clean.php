<?php
declare(strict_types=1);

namespace DocChecker;

use function strlen;

/**
 * Clean copy of check_doc_type functionality for tests (does not include CLI wrapper)
 */

/**
 * Normalize text: lowercase, strip diacritics, collapse whitespace, map OCR confusions
 */
function normalizeText(string $text): string
{
    $t = mb_strtolower($text, 'UTF-8');
    if (function_exists('iconv')) {
        $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t);
        if ($tmp !== false) $t = $tmp;
    }
    $confusions = [
        ' i ' => ' 1 ',
        ' i\b' => '1',
        '\bi ' => '1',
        ' o ' => ' 0 ',
        '(?<=\d)l(?=\d)' => '1',
    ];
    foreach ($confusions as $k => $v) {
        $t = preg_replace("/{$k}/u", $v, $t);
    }
    $t = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $t);
    $t = preg_replace('/\s+/u', ' ', $t);
    return trim($t);
}

function fuzzyMatchKeyword(string $ocr, string $keyword): array
{
    $ocr = normalizeText($ocr);
    $kw = normalizeText($keyword);
    if ($kw === '') return [0.0, ''];
    if (strpos($ocr, $kw) !== false) return [1.0, $kw];
    $ocrWords = preg_split('/\s+/', $ocr);
    $kwWords = preg_split('/\s+/', $kw);
    $n = count($kwWords);
    $best = 0.0; $bestMatch = '';
    $totalWords = count($ocrWords);
    if ($totalWords === 0) return [0.0, ''];
    for ($i = 0; $i + $n - 1 < $totalWords; $i++) {
        $window = array_slice($ocrWords, $i, $n);
        $candidate = implode(' ', $window);
        similar_text($kw, $candidate, $perc);
        $score = $perc / 100.0;
        if ($score > $best) { $best = $score; $bestMatch = $candidate; }
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
    $bestType = 'UNKNOWN'; $bestScore = 0.0; $bestKeyword = '';
    foreach ($lists as $type => $keywords) {
        foreach ($keywords as $kw) {
            [$score, $match] = fuzzyMatchKeyword($ocr, $kw);
            if ($score > $bestScore) { $bestScore = $score; $bestType = $type; $bestKeyword = $kw; }
            if ($bestScore >= 1.0) break 2;
        }
    }
    if ($bestScore >= $threshold) return [$bestType, $bestKeyword, $bestScore];
    foreach ($lists as $type => $keywords) {
        foreach ($keywords as $kw) {
            if (strpos(normalizeText($ocr), normalizeText($kw)) !== false) return [$type, $kw, 1.0];
        }
    }
    return ['UNKNOWN', $bestKeyword, $bestScore];
}

function decideStatus(string $ocr, bool $emailVerified, bool $hasSignature, float $threshold = 0.85): array
{
    $ocrNorm = normalizeText($ocr);
    $ocrLen = mb_strlen(preg_replace('/\s+/', '', $ocrNorm), 'UTF-8');
    [$docType, $matchedKeyword, $matchScore] = detectDocumentType($ocr, $threshold);
    $baseHasKeyword = $docType !== 'UNKNOWN';
    if (! $baseHasKeyword || $ocrLen < 30) {
        return [
            'decision' => 'BLOCKED_BY_AI',
            'reason' => ! $baseHasKeyword ? 'no_keywords' : 'ocr_too_short',
            'code' => 3,
            'docType' => $docType,
            'ocr_length' => $ocrLen,
            'matched_keyword' => $matchedKeyword,
            'match_score' => $matchScore,
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
    ];
}
