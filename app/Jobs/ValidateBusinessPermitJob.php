<?php

namespace App\Jobs;

use App\Mail\BusinessPermitValidated;
use App\Models\DocumentValidation;
use App\Models\User;
use App\Services\DocumentValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ValidateBusinessPermitJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 120;

    protected $userId;
    protected $filePath;
    protected $metadata;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $filePath, array $metadata = [])
    {
        $this->userId = $userId;
        $this->filePath = $filePath;
        $this->metadata = $metadata;
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentValidationService $validationService): void
    {
        try {
            $user = User::find($this->userId);

            if (!$user) {
                Log::warning("ValidateBusinessPermitJob: User {$this->userId} not found");

                return;
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($this->filePath)) {
                Log::warning("ValidateBusinessPermitJob: File {$this->filePath} not found for user {$this->userId}");

                // Create failed validation record
                $validation = DocumentValidation::create([
                    'user_id' => $this->userId,
                    'document_type' => 'business_permit',
                    'file_path' => $this->filePath,
                    'is_valid' => false,
                    'confidence_score' => 0,
                    'validation_status' => 'rejected',
                    'reason' => 'File not found or was deleted',
                    'ai_analysis' => null,
                    'validated_by' => 'system',
                    'validated_at' => now(),
                ]);

                // Send email notification
                try {
                    Mail::to($user->email)->send(new BusinessPermitValidated($user, $validation));
                } catch (\Exception $emailError) {
                    Log::warning('Failed to send email: '.$emailError->getMessage());
                }

                return;
            }

            Log::info("ValidateBusinessPermitJob: Starting validation for user {$this->userId}, file: {$this->filePath}");

            // Check if this is a personal email employer (requires stricter validation)
            $isPersonalEmail = $this->metadata['is_personal_email'] ?? false;
            $minConfidenceRequired = $isPersonalEmail
                ? config('ai.document_validation.business_permit.personal_email_min_confidence', 90)
                : config('ai.document_validation.business_permit.min_confidence', 80);

            // Run AI validation
            $validationResult = $validationService->validateBusinessPermit(
                $this->filePath,
                $this->metadata
            );

            // Apply stricter threshold for personal email employers
            $confidenceScore = $validationResult['confidence'];
            $isValid = $validationResult['valid'];

            // For personal emails (Gmail, Yahoo, etc.), require higher confidence
            if ($isPersonalEmail && $confidenceScore < $minConfidenceRequired) {
                $validationResult['valid'] = false;
                $validationResult['requires_review'] = true;
                $validationResult['reason'] = 'Personal email detected. Higher verification standards applied. '.$validationResult['reason'];

                Log::info("ValidateBusinessPermitJob: Personal email employer flagged for review. Confidence: {$confidenceScore}% (required: {$minConfidenceRequired}%)");
            }

            // Store validation result
            $validation = DocumentValidation::create([
                'user_id' => $this->userId,
                'document_type' => 'business_permit',
                'file_path' => $this->filePath,
                'is_valid' => $validationResult['valid'],
                'confidence_score' => $validationResult['confidence'],
                'validation_status' => $validationResult['valid'] ? 'approved' :
                                      ($validationResult['requires_review'] ? 'pending_review' : 'rejected'),
                'reason' => $validationResult['reason'],
                'ai_analysis' => $validationResult['ai_analysis'],
                'validated_by' => 'ai',
                'validated_at' => now(),
            ]);

            Log::info("ValidateBusinessPermitJob: Validation complete for user {$this->userId}. Status: {$validation->validation_status}, Confidence: {$validation->confidence_score}%");

            // Send email notification to user about validation result
            try {
                Mail::to($user->email)->send(new BusinessPermitValidated($user, $validation));
                Log::info("ValidateBusinessPermitJob: Email notification sent to {$user->email}");
            } catch (\Exception $emailError) {
                Log::warning("ValidateBusinessPermitJob: Failed to send email to {$user->email}: ".$emailError->getMessage());
                // Don't fail the job if email fails
            }

            // If validation failed and auto-reject, optionally delete the file
            if ($validation->validation_status === 'rejected'
                && config('ai.document_validation.business_permit.auto_delete_rejected', false)) {
                Storage::disk('public')->delete($this->filePath);
                Log::info("ValidateBusinessPermitJob: Deleted rejected file {$this->filePath}");
            }
        } catch (\Exception $e) {
            Log::error("ValidateBusinessPermitJob failed for user {$this->userId}: ".$e->getMessage());

            // Create validation record for failed job
            DocumentValidation::create([
                'user_id' => $this->userId,
                'document_type' => 'business_permit',
                'file_path' => $this->filePath,
                'is_valid' => false,
                'confidence_score' => 0,
                'validation_status' => 'pending_review',
                'reason' => 'AI validation failed. Manual review required. Error: '.$e->getMessage(),
                'ai_analysis' => null,
                'validated_by' => 'system',
                'validated_at' => now(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ValidateBusinessPermitJob permanently failed for user {$this->userId} after {$this->tries} attempts: ".$exception->getMessage());

        // Update or create final validation record
        DocumentValidation::updateOrCreate(
            [
                'user_id' => $this->userId,
                'document_type' => 'business_permit',
                'file_path' => $this->filePath,
            ],
            [
                'is_valid' => false,
                'confidence_score' => 0,
                'validation_status' => 'pending_review',
                'reason' => 'Automatic validation failed after multiple attempts. Document flagged for manual review by administrator.',
                'ai_analysis' => ['error' => $exception->getMessage()],
                'validated_by' => 'system',
                'validated_at' => now(),
            ]
        );

        // TODO: Send email to admin about failed validation job
    }
}
