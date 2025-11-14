# AI Document Validation Setup Guide

## Overview

This system uses OpenAI's GPT-4 with Vision (GPT-4o) to automatically validate business permits and resumes uploaded by users. The AI analyzes document images/PDFs to verify authenticity and reject invalid files.

---

## Features

✅ **Business Permit Validation**
- Verifies documents are legitimate business permits (DTI, SEC, Mayor's Permit, etc.)
- Checks for official seals, stamps, and government logos
- Validates registration numbers and dates
- Matches company name with uploaded document
- Detects fake, altered, or random files

✅ **Resume Validation** (Coming Soon)
- Verifies uploaded files are actual resumes/CVs
- Checks for required resume sections
- Rejects random photos or unrelated documents

✅ **Intelligent Decision Making**
- Auto-approve high-confidence valid documents (≥85%)
- Auto-reject clear invalid documents (<50%)
- Flag uncertain cases for manual admin review (50-85%)

✅ **Full Audit Trail**
- Stores all validation results in database
- Records AI confidence scores and analysis
- Tracks approval/rejection reasons
- Maintains validation history per user

---

## Installation & Setup

### 1. Prerequisites

- OpenAI API key with GPT-4o access
- Laravel 11+ application
- Storage configured (public disk)

### 2. Configuration

#### Update .env file:

```bash
# OpenAI Configuration
OPENAI_API_KEY=sk-your-openai-api-key-here
OPENAI_VISION_MODEL=gpt-4o

# Enable Document Validation
AI_DOCUMENT_VALIDATION=true
AI_VALIDATE_BUSINESS_PERMIT=true
AI_VALIDATE_RESUME=true

# Confidence Thresholds (0-100)
AI_BUSINESS_PERMIT_MIN_CONFIDENCE=80
AI_RESUME_MIN_CONFIDENCE=70
```

#### Configuration Details:

| Variable | Default | Description |
|----------|---------|-------------|
| `AI_DOCUMENT_VALIDATION` | `true` | Master switch for all document validation |
| `AI_VALIDATE_BUSINESS_PERMIT` | `true` | Enable business permit validation |
| `AI_VALIDATE_RESUME` | `true` | Enable resume validation |
| `AI_BUSINESS_PERMIT_MIN_CONFIDENCE` | `80` | Minimum confidence score for approval |
| `AI_RESUME_MIN_CONFIDENCE` | `70` | Minimum confidence for resume approval |

### 3. Run Migration

```bash
php artisan migrate
```

This creates the `document_validations` table to store validation results.

---

## How It Works

### Business Permit Validation Flow

```
1. User uploads business permit (PDF/JPG/PNG)
   ↓
2. File stored temporarily in storage/business_permits/temp/
   ↓
3. AI analyzes document using GPT-4o Vision API
   - Checks if it's a business permit
   - Verifies official seals/stamps
   - Validates registration number
   - Compares company name
   - Detects fraud indicators
   ↓
4. AI returns analysis:
   {
     "is_business_permit": true/false,
     "confidence_score": 0-100,
     "appears_authentic": true/false,
     "has_official_seals": true/false,
     "recommendation": "APPROVE/REJECT/MANUAL_REVIEW"
   }
   ↓
5. System decides:
   - Confidence ≥ 85% + APPROVE → Auto-approve ✅
   - Confidence < 50% or REJECT → Auto-reject ❌
   - In between (50-84%) → Manual review 👨‍💼
   ↓
6. If approved:
   - Move to permanent storage
   - Create user account
   - Log validation result
   
   If rejected:
   - Delete file
   - Show error to user
   - Log rejection reason
   
   If manual review:
   - Keep file in temp
   - Notify admin
   - User waits for decision
```

---

## Usage Examples

### Employer Registration

When an employer registers with a business permit:

```php
// In RegisterController
$validationResult = $documentValidationService->validateBusinessPermit(
    $tempFilePath,
    [
        'company_name' => 'ABC Corporation',
        'email' => 'hr@abccorp.com',
    ]
);

if (!$validationResult['valid']) {
    return back()->with('error', $validationResult['reason']);
}

// Continue registration...
```

### Profile Update

When employer updates business permit in settings:

```php
// In ProfileController
$validationResult = $documentValidationService->validateBusinessPermit(
    $tempFilePath,
    [
        'company_name' => $user->company_name,
        'email' => $user->email,
    ]
);

if (!$validationResult['valid']) {
    return redirect()->route('settings')
        ->with('error', $validationResult['reason']);
}
```

---

## Validation Results Structure

### Success Response

```php
[
    'valid' => true,
    'confidence' => 95,
    'reason' => 'Valid DTI business registration certificate with official seal',
    'requires_review' => false,
    'ai_analysis' => [
        'document_type' => 'DTI Business Registration',
        'has_official_seals' => true,
        'has_registration_number' => true,
        'business_name_matches' => true,
        'appears_authentic' => true,
        'is_expired' => false,
        'issuing_authority' => 'Department of Trade and Industry',
        'validity_dates' => '2024-01-15 to 2025-01-14',
        'recommendation' => 'APPROVE',
        'red_flags' => []
    ]
]
```

### Rejection Response

```php
[
    'valid' => false,
    'confidence' => 15,
    'reason' => 'Document appears to be a personal photo, not a business permit',
    'requires_review' => false,
    'ai_analysis' => [
        'document_type' => 'Personal photograph',
        'has_official_seals' => false,
        'has_registration_number' => false,
        'appears_authentic' => false,
        'recommendation' => 'REJECT',
        'red_flags' => [
            'No official government seals or stamps',
            'No registration number visible',
            'Not a business document'
        ]
    ]
]
```

### Manual Review Required

```php
[
    'valid' => false,
    'confidence' => 65,
    'reason' => 'Document quality is poor. Manual verification recommended.',
    'requires_review' => true,
    'ai_analysis' => [
        'document_type' => 'Possible business permit (unclear)',
        'has_official_seals' => null,
        'appears_authentic' => null,
        'recommendation' => 'MANUAL_REVIEW',
        'red_flags' => [
            'Image quality too low for confident analysis',
            'Partial document visible'
        ]
    ]
]
```

---

## Database Schema

### document_validations Table

```sql
CREATE TABLE document_validations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    document_type VARCHAR(255), -- 'business_permit', 'resume'
    file_path VARCHAR(255),
    is_valid BOOLEAN DEFAULT 0,
    confidence_score INT DEFAULT 0,
    validation_status VARCHAR(255), -- 'approved', 'rejected', 'pending_review'
    reason TEXT,
    ai_analysis JSON,
    validated_by VARCHAR(255), -- 'ai', 'manual', or admin user ID
    validated_at TIMESTAMP,
    admin_notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Query Examples

```php
// Get all pending reviews
DocumentValidation::pendingReview()->get();

// Get business permit validations
DocumentValidation::ofType('business_permit')->get();

// Get AI-validated documents
DocumentValidation::aiValidated()->get();

// Get user's validation history
$user->documentValidations()->latest()->get();

// Get rejected documents
DocumentValidation::rejected()->get();
```

---

## Cost Considerations

### OpenAI Pricing (GPT-4o)

- **Input**: ~$5.00 per 1M tokens
- **Output**: ~$15.00 per 1M tokens

### Estimated Costs per Validation

- **Business Permit**: ~$0.01 - $0.02 per validation
  - Image size: ~500KB average
  - Tokens: ~800-1200 input + 200-400 output
  
### Monthly Cost Estimates

| Registrations/Month | Estimated Cost |
|---------------------|----------------|
| 100 | $1.50 |
| 500 | $7.50 |
| 1,000 | $15.00 |
| 5,000 | $75.00 |

### Cost Optimization Tips

1. **Compress images** before sending to API
2. **Use smaller vision model** if accuracy allows (gpt-4-vision-preview)
3. **Cache validation results** (already implemented)
4. **Batch process** during off-peak hours for manual reviews

---

## Disabling Document Validation

To disable AI validation (fall back to basic file type checking):

```bash
# In .env
AI_DOCUMENT_VALIDATION=false
# or
AI_VALIDATE_BUSINESS_PERMIT=false
```

When disabled:
- Basic file type/size validation still applies
- No AI analysis performed
- All documents marked for manual review
- No OpenAI API calls made (zero cost)

---

## Troubleshooting

### "OpenAI API key is not configured"

**Solution**: Add your API key to `.env`:
```bash
OPENAI_API_KEY=sk-your-key-here
```

### "Unable to validate document automatically"

**Causes**:
- AI service unavailable
- API rate limit exceeded
- Invalid API key
- Network issues

**Solution**: Check logs in `storage/logs/laravel.log`

### "Document requires manual review"

**Causes**:
- Low image quality
- Partial document visible
- Confidence score between thresholds
- Unusual document format

**Solution**: Admin should manually review in pending queue

### High Rejection Rate

**Possible Issues**:
- Users uploading wrong files
- Prompt needs adjustment
- Confidence thresholds too strict

**Solution**: Review AI analysis in database, adjust thresholds in config

---

## Security Considerations

✅ **Implemented Security Measures:**

1. **File Type Validation**: Only PDF, JPG, JPEG, PNG allowed
2. **File Size Limits**: Max 5MB per file
3. **Temporary Storage**: Files stored in temp until validated
4. **Auto-deletion**: Failed validations delete files immediately
5. **Database Logging**: Full audit trail of all validations
6. **API Key Protection**: Never exposed to client-side code

⚠️ **Additional Recommendations:**

- Regularly review pending validations
- Monitor for unusual patterns (mass rejections)
- Set up alerts for validation failures
- Periodic audit of approved documents

---

## Testing

### Test with Valid Document

Upload a real business permit:
- DTI registration
- SEC certificate
- Mayor's permit
- Business license

Expected: ✅ Auto-approved (confidence ≥85%)

### Test with Invalid Document

Upload random files:
- Personal photo
- Screenshot
- Blank PDF
- Invoice/receipt

Expected: ❌ Auto-rejected with clear reason

### Test with Edge Cases

- Blurry permit photo
- Partial document scan
- Foreign language permit
- Expired permit

Expected: 👨‍💼 Flagged for manual review

---

## Admin Dashboard (Future Feature)

Planned admin interface for manual review:

```
Pending Reviews Queue
┌─────────────────────────────────────────────────┐
│ Company: ABC Corp                    Confidence: 67% │
│ Document: DTI-2024-12345.pdf                        │
│ AI Says: "Unclear image quality"                    │
│ [View Document] [Approve] [Reject]                  │
└─────────────────────────────────────────────────┘
```

---

## API Reference

### DocumentValidationService

#### `validateBusinessPermit(string $filePath, array $metadata): array`

Validates a business permit document.

**Parameters:**
- `$filePath` (string): Path to file in storage
- `$metadata` (array): Context data
  - `company_name` (string): Expected company name
  - `email` (string): User email

**Returns:**
```php
[
    'valid' => bool,
    'confidence' => int (0-100),
    'reason' => string,
    'requires_review' => bool,
    'ai_analysis' => array|null
]
```

#### `validateResume(string $filePath, array $metadata): array`

Validates a resume/CV document.

**Parameters:**
- `$filePath` (string): Path to resume file
- `$metadata` (array): Context data
  - `user_name` (string): Expected user name

**Returns:** Same structure as `validateBusinessPermit`

---

## Support

For issues or questions:

1. Check `storage/logs/laravel.log`
2. Review AI analysis in `document_validations` table
3. Verify `.env` configuration
4. Test with known valid documents

---

## Version History

**v1.0.0** - Initial Release
- Business permit validation
- GPT-4o integration
- Auto-approve/reject logic
- Manual review flagging
- Database logging
- Resume validation (placeholder)

---

## License

This feature is part of the Job Recommendation System and follows the same license terms.
