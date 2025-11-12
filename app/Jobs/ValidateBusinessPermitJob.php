<?php

namespace App\Jobs;

use App\Mail\BusinessPermitValidated;
use App\Models\DocumentValidation;
use App\Models\Notification;
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

            // === DUPLICATE DETECTION: File Hash + Company Name ===
            $duplicateCheck = $this->checkForDuplicatePermit($user);
            $duplicateType = $duplicateCheck['details']['duplicate_type'] ?? null;

            // If it's a definite duplicate by file hash (or both), short-circuit and flag for review
            if ($duplicateCheck['is_duplicate'] && $duplicateType !== 'company_name') {
                Log::warning("ValidateBusinessPermitJob: Duplicate permit detected for user {$this->userId}. Reason: {$duplicateCheck['reason']}");
                
                // Create validation record flagged for manual review
                $validation = DocumentValidation::create([
                    'user_id' => $this->userId,
                    'document_type' => 'business_permit',
                    'file_path' => $this->filePath,
                    'file_hash' => $duplicateCheck['details']['file_hash'] ?? null,
                    'is_valid' => false,
                    'confidence_score' => 0,
                    'validation_status' => 'pending_review',
                    'reason' => $duplicateCheck['reason'],
                    'ai_analysis' => [
                        'duplicate_detection' => $duplicateCheck['details'],
                        'requires_admin_review' => true,
                    ],
                    'validated_by' => 'system',
                    'validated_at' => now(),
                    'permit_expiry_date' => null,
                    'expiry_reminder_sent' => false,
                ]);

                // Notify user about duplicate
                Notification::create([
                    'user_id' => $this->userId,
                    'type' => 'warning',
                    'title' => 'Business Permit Requires Review',
                    'message' => 'Your business permit has been flagged for manual review. ' . $duplicateCheck['user_message'],
                    'read' => false,
                ]);

                // Send email notification
                try {
                    Mail::to($user->email)->send(new BusinessPermitValidated($user, $validation));
                } catch (\Exception $emailError) {
                    Log::warning('Failed to send email: '.$emailError->getMessage());
                }

                return;
            }

            // For company_name-only duplicates, proceed with AI to extract permit_number and context, but keep review status later
            $companyDuplicateOnly = ($duplicateCheck['is_duplicate'] && $duplicateType === 'company_name');

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

            // NOTE: Auto-approval by AI is potentially unsafe (false positives like resumes/random PDFs).
            // Respect an explicit config flag to allow auto-approval. By default, we force manual review
            // so that all permit-like uploads remain pending until an admin reviews them.
            // To enable auto-approve in the future, set:
            // ai.document_validation.business_permit.allow_auto_approve = true
            $aiAnalysis = $validationResult['ai_analysis'] ?? [];
            $autoApproved = false;
            $allowAutoApprove = config('ai.document_validation.business_permit.allow_auto_approve', false);

            if ($allowAutoApprove) {
                // Preserve existing auto-approval logic only when explicitly enabled in config.
                $autoApproveThreshold = config('ai.document_validation.business_permit.auto_approve_threshold', 85);
                $looseAutoApproveThreshold = config('ai.document_validation.business_permit.loose_auto_approve_threshold', 75);
                $looseDocTypes = config('ai.document_validation.business_permit.loose_auto_approve_doc_types', ['barangay', 'mayor', 'dti']);

                // Normalize detected document type for loose matching
                $documentTypeLower = strtolower($aiAnalysis['document_type'] ?? '');
                $documentTypeIsLoose = false;
                foreach ($looseDocTypes as $dt) {
                    if ($dt && strpos($documentTypeLower, $dt) !== false) {
                        $documentTypeIsLoose = true;
                        break;
                    }
                }

                if ($user->hasVerifiedEmail() && isset($aiAnalysis['business_name_matches']) && $aiAnalysis['business_name_matches'] === true) {
                    $hasSignatureOrSeal = (!empty($aiAnalysis['has_signature']) && $aiAnalysis['has_signature'] === true)
                        || (!empty($aiAnalysis['has_official_seals']) && $aiAnalysis['has_official_seals'] === true);

                    // Auto-approve when signature/seal + high confidence, OR for certain permit types (barangay/mayor/DTI)
                    // allow a looser threshold if business name matches and email is verified.
                    $canAutoApproveByStrict = $hasSignatureOrSeal && $confidenceScore >= $autoApproveThreshold;
                    $canAutoApproveByLooseType = $documentTypeIsLoose && $confidenceScore >= $looseAutoApproveThreshold;

                    if ($canAutoApproveByStrict || $canAutoApproveByLooseType) {
                        // Force approval
                        $validationResult['valid'] = true;
                        $validationResult['requires_review'] = false;
                        $validationResult['reason'] = 'Auto-approved: verified email + AI match with sufficient confidence.';
                        $autoApproved = true;
                        Log::info("ValidateBusinessPermitJob: Auto-approved permit for user {$this->userId} via AI match. Confidence={$confidenceScore}, document_type={$aiAnalysis['document_type']}");
                    }
                }
            } else {
                // Default behavior: do NOT auto-approve. Force manual review for anything that would otherwise be accepted.
                if ($validationResult['valid']) {
                    $validationResult['requires_review'] = true;
                    $validationResult['valid'] = false; // ensure it's marked pending_review below
                    $validationResult['reason'] = ($validationResult['reason'] ?? '') . ' Pending manual review by an administrator.';
                }
            }

            // Enforce business-name match. If AI indicates a mismatch with the registered company name,
            // do not auto-approve; force manual review and show a clear reason.
            if (array_key_exists('business_name_matches', $aiAnalysis) && $aiAnalysis['business_name_matches'] === false) {
                $validationResult['valid'] = false;
                $validationResult['requires_review'] = true;
                $validationResult['reason'] = "Business name on the permit doesn't match your registered company name. Each account is tied to one business permit only.";
            }

            // Post-AI duplicate check by permit_number
            $permitNumber = $validationResult['permit_number'] ?? null;
            $postAiDuplicate = null;
            if ($permitNumber) {
                $postAiDuplicate = DocumentValidation::where('document_type', 'business_permit')
                    ->where('user_id', '!=', $this->userId)
                    ->where('validation_status', 'approved')
                    ->where('permit_number', $permitNumber)
                    ->first();

                if ($postAiDuplicate) {
                    // Override to manual review due to same permit number on another account
                    $validationResult['valid'] = false;
                    $validationResult['requires_review'] = true;
                    $validationResult['reason'] = 'This permit/registration number is already registered to another account. Manual review required.';

                    // Enrich AI analysis with duplicate-by-number details
                    $aiAnalysis = $validationResult['ai_analysis'] ?? [];
                    $aiAnalysis['duplicate_detection'] = array_merge($aiAnalysis['duplicate_detection'] ?? [], [
                        'duplicate_type' => isset($aiAnalysis['duplicate_detection']['duplicate_type'])
                            ? $aiAnalysis['duplicate_detection']['duplicate_type'] . '+permit_number'
                            : 'permit_number',
                        'permit_number_match' => true,
                        'permit_number' => $permitNumber,
                        'existing_user_email' => $postAiDuplicate->user->email ?? null,
                        'existing_validation_id' => $postAiDuplicate->id,
                    ]);
                    $validationResult['ai_analysis'] = $aiAnalysis;
                }
            }

            // If company-name duplicate was detected earlier, force pending review with explanation if not already forced
            // Allow override when we already auto-approved via verified email + AI match
            if ($companyDuplicateOnly && !$postAiDuplicate && !$autoApproved) {
                $validationResult['valid'] = false;
                $validationResult['requires_review'] = true;
                $validationResult['reason'] = "The company name '{$user->company_name}' is already registered to another account. If this is a branch office or a renewed permit, admin approval is required.";
                $aiAnalysis = $validationResult['ai_analysis'] ?? [];
                $aiAnalysis['duplicate_detection'] = array_merge($aiAnalysis['duplicate_detection'] ?? [], [
                    'duplicate_type' => 'company_name',
                    'company_name_match' => true,
                    'existing_user_email' => $duplicateCheck['details']['existing_user_email'] ?? null,
                    'existing_validation_id' => $duplicateCheck['details']['existing_validation_id'] ?? null,
                    'permit_number' => $permitNumber,
                    'permit_number_match' => false,
                ]);
                $validationResult['ai_analysis'] = $aiAnalysis;
            }

            // Store validation result
            $validation = DocumentValidation::create([
                'user_id' => $this->userId,
                'document_type' => 'business_permit',
                'file_path' => $this->filePath,
                'file_hash' => $duplicateCheck['file_hash'] ?? null,
                'permit_number' => $permitNumber,
                'is_valid' => $validationResult['valid'],
                'confidence_score' => $validationResult['confidence'],
                'validation_status' => $validationResult['valid'] ? 'approved' :
                                      ($validationResult['requires_review'] ? 'pending_review' : 'rejected'),
                'reason' => $validationResult['reason'],
                'ai_analysis' => $validationResult['ai_analysis'],
                'validated_by' => 'ai',
                'validated_at' => now(),
                'permit_expiry_date' => $validationResult['permit_expiry_date'] ?? null,
                'expiry_reminder_sent' => false,
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

            // Notify admins when the permit was auto-approved by AI (strict or loose path)
            if (!empty($autoApproved)) {
                try {
                    // Determine method label
                    $method = (!empty($documentTypeIsLoose) && $documentTypeIsLoose) ? 'loose' : 'strict';
                    \App\Services\AdminNotificationService::notifyPermitAutoApproved($user, $validation, $method);
                    Log::info("ValidateBusinessPermitJob: Admins notified of auto-approval for user {$this->userId} (method={$method})");
                } catch (\Exception $notifyErr) {
                    Log::warning('Failed to notify admins of auto-approval: '.$notifyErr->getMessage());
                }
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

    /**
     * Check for duplicate business permit (file hash + company name).
     *
     * @param User $user
     * @return array
     */
    protected function checkForDuplicatePermit(User $user): array
    {
        $companyName = $user->company_name;
        
        // Calculate file hash of the uploaded permit
        $filePath = Storage::disk('public')->path($this->filePath);
        $fileHash = hash_file('sha256', $filePath);

        // Check 1: File Hash - Has this exact file been uploaded before?
        $duplicateByHash = DocumentValidation::where('document_type', 'business_permit')
            ->where('user_id', '!=', $this->userId) // Exclude current user
            ->where('file_hash', $fileHash)
            ->where('validation_status', 'approved')
            ->first();

        // Check 2: Company Name - Is this company already registered?
        $duplicateByCompany = null;
        if ($companyName) {
            $duplicateByCompany = DocumentValidation::where('document_type', 'business_permit')
                ->where('user_id', '!=', $this->userId)
                ->where('validation_status', 'approved')
                ->whereHas('user', function ($query) use ($companyName) {
                    $query->where('company_name', $companyName);
                })
                ->first();
        }

        // Build response
        if ($duplicateByHash && $duplicateByCompany) {
            // Both file and company match - highly suspicious
            return [
                'is_duplicate' => true,
                'reason' => 'This business permit file and company name are already registered to another account. This appears to be a duplicate registration.',
                'user_message' => 'Our system detected that this business permit is already registered to another account. If this is a mistake, please contact support.',
                'details' => [
                    'duplicate_type' => 'both',
                    'file_hash_match' => true,
                    'company_name_match' => true,
                    'file_hash' => $fileHash,
                    'existing_user_email' => $duplicateByCompany->user->email ?? null,
                    'existing_validation_id' => $duplicateByCompany->id,
                ],
            ];
        } elseif ($duplicateByHash) {
            // Same file, different company name
            return [
                'is_duplicate' => true,
                'reason' => 'This exact business permit file has already been uploaded by another account. Each business should have a unique permit.',
                'user_message' => 'This permit file appears to be a duplicate. If you believe this is an error, an administrator will review your submission.',
                'details' => [
                    'duplicate_type' => 'file_hash',
                    'file_hash_match' => true,
                    'company_name_match' => false,
                    'file_hash' => $fileHash,
                    'existing_user_email' => $duplicateByHash->user->email ?? null,
                ],
            ];
        } elseif ($duplicateByCompany) {
            // Same company name, different file (might be renewed permit or branch)
            return [
                'is_duplicate' => true,
                'reason' => "The company name '{$companyName}' is already registered to another account. If this is a branch office or renewed permit, admin approval is required.",
                'user_message' => 'This company name is already registered. An administrator will review your permit to verify it\'s a legitimate branch or renewal.',
                'details' => [
                    'duplicate_type' => 'company_name',
                    'file_hash_match' => false,
                    'company_name_match' => true,
                    'file_hash' => $fileHash,
                    'existing_user_email' => $duplicateByCompany->user->email ?? null,
                    'existing_validation_id' => $duplicateByCompany->id,
                ],
            ];
        }

        // No duplicates found - return the hash to be stored
        return [
            'is_duplicate' => false,
            'reason' => null,
            'user_message' => null,
            'details' => null,
            'file_hash' => $fileHash, // Store this for future duplicate checks
        ];
    }
}
