<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\PermitVerifierService;

class PermitVerifierTextTest extends TestCase
{
    public function test_mandaluyong_locational_clearance_is_detected()
    {
        $svc = new PermitVerifierService();

        $sample = <<<'TXT'
Republic of the Philippines
Mandaluyong City
OFFICE OF THE PUNONG BARANGAY
BARANGAY ADDITION HILLS

BUSINESS LOCATIONAL CLEARANCE
Pursuant to article IV. Letter C of R.A. 7160 known as Local Government
Code of 1991 this Barangay Locational Clearance is issued to:
MARGIE STORE
MARGARITA P. MONDERO
FIRM NAME
PH. 3 LOT 12 BLK E. BLK 40 Brgy. Addition Hills
Mandaluyong City
BUSINESS ADDRESS

SARI-SARI STORE
NATURE OF BUSINESS

Issued this 20TH day of MAY 20 25
TXT;

        $result = $svc->verifyText($sample);

        $this->assertIsArray($result);
        $this->assertEquals('PENDING', $result['status']);
        $this->assertEquals('BARANGAY_LOCATIONAL_CLEARANCE', $result['document_type']);
    }
}
