<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../scripts/check_doc_type.php';

final class DetectTest2 extends TestCase
{
    public function testClearBusinessRegistration()
    {
        $ocr = "Certificate of Business Name Registration\nBusiness Name: ACME Corp\n...";
        $r = \DocChecker\decideStatus($ocr, true, true, 0.85);
        $this->assertEquals('AUTO_APPROVED', $r['decision']);
        $this->assertEquals(0, $r['code']);
    }

    public function testClearBarangayClearance()
    {
        $ocr = "Republic of the Philippines\nBarangay clearance\nThis is to certify...";
        $r = \DocChecker\decideStatus($ocr, true, true, 0.85);
        $this->assertEquals('AUTO_APPROVED', $r['decision']);
        $this->assertEquals(0, $r['code']);
    }

    public function testShortUnrelatedTextBlocked()
    {
        $ocr = "Hello world";
        $r = \DocChecker\decideStatus($ocr, false, false, 0.85);
        $this->assertEquals('BLOCKED_BY_AI', $r['decision']);
        $this->assertEquals(3, $r['code']);
    }

    public function testNoisyOcrFuzzyMatch()
    {
        // Simulate OCR noise: 'barangay' -> 'barngay'
        $ocr = "Republic of the Philippines\nBarngay Locational Clearence\nIssued this 2020";
        $r = \DocChecker\decideStatus($ocr, true, true, 0.7);
        $this->assertNotEquals('BLOCKED_BY_AI', $r['decision']);
    }

    public function testSignedButEmailUnverifiedGoesToReview()
    {
        $ocr = "Barangay Clearance\nIssued this...";
        $r = \DocChecker\decideStatus($ocr, false, true, 0.85);
        $this->assertEquals('REVIEW_BY_ADMIN', $r['decision']);
        $this->assertEquals(2, $r['code']);
    }

    public function testLongTextNoKeywordsBlocked()
    {
        $ocr = str_repeat('Lorem ipsum dolor sit amet ', 10);
        $r = \DocChecker\decideStatus($ocr, true, true, 0.85);
        $this->assertEquals('BLOCKED_BY_AI', $r['decision']);
    }
}
