<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Models\User;
use App\Models\DocumentValidation;
use App\Models\EmployerDocument;

class EmployerPermitVerifierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure storage fake so files don't hit disk
        Storage::fake('public');
        // Prevent queued jobs from running during these controller integration tests
        Queue::fake();
    }

    public function test_verifier_blocks_invalid_document()
    {
        // Arrange: create employer user
        $user = User::factory()->create(['user_type' => 'employer']);
        $this->actingAs($user);
        // ensure verifier url env is set so controller will call the service
        putenv('VERIFIER_SERVICE_URL=http://verifier.test/upload_document');

        // Fake verifier response: BLOCKED
        Http::fake([
            '*' => Http::response(['status' => 'BLOCKED', 'reason' => 'unrecognized_document_type'], 200)
        ]);

        // upload fake pdf
        $file = UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf');

        $resp = $this->post('/employer/permit/reupload', [
            'business_permit' => $file,
        ]);

    $resp->assertRedirect();

        // Ensure no DocumentValidation or EmployerDocument created
        $this->assertEquals(0, DocumentValidation::count());
        $this->assertEquals(0, EmployerDocument::count());
    }

    public function test_verifier_accepts_and_creates_pending_on_valid_document()
    {
        $user = User::factory()->create(['user_type' => 'employer']);
        $this->actingAs($user);
        putenv('VERIFIER_SERVICE_URL=http://verifier.test/upload_document');

        // Fake verifier response: PENDING_MANUAL_REVIEW with extracted data
        Http::fake([
            '*' => Http::response([
                'status' => 'PENDING_MANUAL_REVIEW',
                'ai_confidence' => 'High',
                'extracted_data' => ['document_type' => 'DTI_BUSINESS_NAME_REGISTRATION', 'firm_name' => 'MARGARITA SARI-SARI STORE']
            ], 200)
        ]);

        $file = UploadedFile::fake()->create('permit.pdf', 200, 'application/pdf');

        $resp = $this->post('/employer/permit/reupload', [
            'business_permit' => $file,
        ]);

    $resp->assertRedirect();

        $this->assertEquals(1, DocumentValidation::count());
        $this->assertEquals(1, EmployerDocument::count());

        $doc = DocumentValidation::first();
        $this->assertEquals('pending_review', $doc->validation_status);
    }
}
