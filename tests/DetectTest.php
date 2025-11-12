<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../scripts/check_doc_type.php';

final class DetectTest extends TestCase
{
    private string $cleanSample = "BUSINESS LOCATIONAL CLEARANCE
MARGIE STORE
MARGARITA P. MONDERO
PH. 3 LOT 12 BLK E. BLK 40 BRGY. ADDITION HILLS
MANDALUYONG CITY
SARI-SARI STORE";

    private string $noisySample = "BUS1NESS LOCAT10NAL C L E A R A N C E
MARG1E STORE
MARGARITA P MONDER0
PH. 3 L0T 12 BLK E. BLK 40 BRGY ADD1T10N H1LLS
MANDALUY0NG C1TY
SARI-SAR1 STORE";

    public function testClearBusinessRegistrationAutoApproved()
    {
        $r = \DocChecker\decideStatus($this->cleanSample, true, true, 0.85);
        $this->assertEquals('AUTO_APPROVED', $r['decision']);
        $this->assertEquals(0, $r['code']);
    }

    public function testClearBarangayClearanceAutoApproved()
    {
        $ocr = "REPUBLIC OF THE PHILIPPINES\nBARANGAY CLEARANCE\nThis is to certify...\n";
        $r = \DocChecker\decideStatus($ocr, true, true, 0.85);
        $this->assertEquals('AUTO_APPROVED', $r['decision']);
        $this->assertEquals(0, $r['code']);
    }

    public function testShortUnrelatedTextBlocked()
    {
        $r = \DocChecker\decideStatus('Hello world', false, false, 0.85);
        $this->assertEquals('BLOCKED_BY_AI', $r['decision']);
        $this->assertEquals(3, $r['code']);
    }

    public function testNoisyOcrFuzzyMatch()
    {
        $r = \DocChecker\decideStatus($this->noisySample, true, true, 0.8);
        // Should not be blocked; fuzzy matching should find a candidate
        $this->assertNotEquals('BLOCKED_BY_AI', $r['decision']);
        $this->assertArrayHasKey('match_score', $r);
        $this->assertGreaterThan(0.5, $r['match_score']);
    }

    public function testSignedButEmailUnverifiedGoesToReview()
    {
        $r = \DocChecker\decideStatus("Barangay Clearance\nIssued this...\nMore details to exceed length", false, true, 0.85);
        $this->assertEquals('REVIEW_BY_ADMIN', $r['decision']);
        $this->assertEquals(2, $r['code']);
    }

    public function testLongTextNoKeywordsBlocked()
    {
        $long = str_repeat('Lorem ipsum dolor sit amet ', 10);
        $r = \DocChecker\decideStatus($long, true, true, 0.85);
        $this->assertEquals('BLOCKED_BY_AI', $r['decision']);
    }
}
