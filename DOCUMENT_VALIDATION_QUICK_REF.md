# Document Validation Quick Reference

## 🚀 Quick Start

1. **Add API key to .env:**
   ```bash
   OPENAI_API_KEY=sk-your-key-here
   OPENAI_VISION_MODEL=gpt-4o
   AI_DOCUMENT_VALIDATION=true
   ```

2. **Run migration:**
   ```bash
   php artisan migrate
   ```

3. **Test:** Upload a business permit during employer registration

---

## ✅ What Gets Validated

| Document Type | Status | Auto-Validation |
|--------------|--------|-----------------|
| Business Permit | ✅ Active | Yes |
| Resume | 🔄 Planned | Coming soon |

---

## 🎯 Validation Decisions

| Confidence Score | Status | Action |
|-----------------|--------|--------|
| ≥ 85% | ✅ Approved | Auto-approve, create account |
| 50-84% | ⚠️ Review | Flag for manual review |
| < 50% | ❌ Rejected | Auto-reject, show error |

---

## 📝 Business Permit Validation Criteria

**ACCEPTED:**
- ✅ DTI Business Registration
- ✅ SEC Certificate
- ✅ Mayor's Permit
- ✅ Business License
- ✅ BIR Registration

**REJECTED:**
- ❌ Personal photos/selfies
- ❌ Random screenshots
- ❌ Blank documents
- ❌ Receipts (non-registration)
- ❌ Expired permits (>1 year)
- ❌ Obviously fake/altered docs

**AI Checks For:**
- Official government seals/stamps
- Registration/permit number
- Issuing authority
- Validity dates
- Business name match
- Document authenticity

---

## 🔧 Configuration Quick Reference

```bash
# .env Settings

# Enable/Disable
AI_DOCUMENT_VALIDATION=true          # Master switch
AI_VALIDATE_BUSINESS_PERMIT=true    # Business permits
AI_VALIDATE_RESUME=true              # Resumes (future)

# AI Model
OPENAI_API_KEY=sk-...                # Required
OPENAI_VISION_MODEL=gpt-4o          # GPT-4 with vision

# Thresholds (0-100)
AI_BUSINESS_PERMIT_MIN_CONFIDENCE=80 # Minimum to approve
AI_RESUME_MIN_CONFIDENCE=70          # Minimum for resume
```

---

## 💾 Database Queries

```php
// Pending manual reviews
DocumentValidation::pendingReview()->get();

// All business permits
DocumentValidation::ofType('business_permit')->get();

// Approved documents
DocumentValidation::approved()->get();

// Rejected with reasons
DocumentValidation::rejected()->get();

// User's validation history
User::find($id)->documentValidations;

// Recent AI validations
DocumentValidation::aiValidated()
    ->latest()
    ->limit(50)
    ->get();

// Low confidence validations
DocumentValidation::where('confidence_score', '<', 70)->get();
```

---

## 💰 Cost Calculator

| Monthly Registrations | Estimated Cost |
|----------------------|----------------|
| 100 | $1.50 |
| 500 | $7.50 |
| 1,000 | $15.00 |
| 5,000 | $75.00 |
| 10,000 | $150.00 |

**Cost per validation:** ~$0.01-$0.02

---

## 🔍 Testing Checklist

- [ ] **Valid Business Permit**
  - Upload real DTI/SEC certificate
  - Expected: ✅ Auto-approved
  
- [ ] **Invalid Document**
  - Upload personal photo
  - Expected: ❌ Rejected with reason
  
- [ ] **Edge Cases**
  - Blurry image → Manual review
  - Expired permit → Rejected
  - Foreign permit → Manual review
  - Partial scan → Manual review

---

## 🐛 Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| "API key not configured" | Add `OPENAI_API_KEY` to `.env` |
| "Unable to validate" | Check logs in `storage/logs/laravel.log` |
| All docs rejected | Lower confidence threshold |
| API rate limit | Wait or upgrade OpenAI plan |
| High costs | Compress images, reduce max tokens |

---

## 📊 Validation Status Meanings

| Status | Database Value | Description |
|--------|---------------|-------------|
| Approved | `approved` | Document is valid, user can proceed |
| Rejected | `rejected` | Invalid document, user must re-upload |
| Pending Review | `pending_review` | Admin must manually verify |

---

## 🔐 File Validation Rules

```php
// Employer Registration & Profile Update
'business_permit' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'

// Breakdown:
- Required: Must upload a file
- Type: PDF, JPG, JPEG, PNG only
- Size: Max 5MB (5120 KB)
```

**After upload:**
1. File type check ✅
2. File size check ✅
3. **AI content validation** ✅ (new!)
4. Store if valid ✅

---

## 📍 Where Validation Happens

### 1. Employer Registration
**File:** `app/Http/Controllers/Auth/RegisterController.php`
**Line:** ~55-120
**Trigger:** User submits registration form with business permit

### 2. Profile Update
**File:** `app/Http/Controllers/ProfileController.php`
**Line:** ~370-435
**Trigger:** Employer uploads new business permit in settings

---

## 🎨 User Experience Flow

```
┌─────────────────────────────────────────┐
│ User uploads business permit            │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│ AI analyzes document (2-5 seconds)      │
└──────────────┬──────────────────────────┘
               │
      ┌────────┴────────┐
      │                 │
      ▼                 ▼
┌──────────┐      ┌──────────┐
│  Valid?  │      │ Invalid? │
└─────┬────┘      └─────┬────┘
      │                 │
      ▼                 ▼
┌──────────┐      ┌──────────┐
│ ✅ Success│      │ ❌ Error  │
│ Account  │      │ "Invalid │
│ created  │      │  permit" │
└──────────┘      └──────────┘
      │
      ▼
┌──────────┐
│ Pending  │
│ Review   │
│ (notify) │
└──────────┘
```

---

## 📂 File Structure

```
app/
├── Services/
│   └── DocumentValidationService.php    # Main AI logic
├── Models/
│   └── DocumentValidation.php           # Database model
└── Http/Controllers/
    ├── Auth/
    │   └── RegisterController.php       # Registration validation
    └── ProfileController.php            # Profile update validation

config/
└── ai.php                                # AI configuration

database/migrations/
└── 2025_11_03_080000_create_document_validations_table.php

storage/
└── app/public/
    └── business_permits/
        ├── temp/                         # Temporary uploads
        └── [filename]                    # Validated permits
```

---

## 🚨 Emergency Disable

If AI validation causes issues:

**Option 1: Disable in .env**
```bash
AI_DOCUMENT_VALIDATION=false
```

**Option 2: Disable business permits only**
```bash
AI_VALIDATE_BUSINESS_PERMIT=false
```

**Result:** System falls back to basic file validation only (no AI, no cost)

---

## 📞 Support Checklist

Before asking for help:

1. ✅ Check `OPENAI_API_KEY` is set in `.env`
2. ✅ Run `php artisan config:clear`
3. ✅ Check `storage/logs/laravel.log` for errors
4. ✅ Verify file permissions on `storage/` folder
5. ✅ Test with a known valid business permit
6. ✅ Check OpenAI account has credits
7. ✅ Verify `document_validations` table exists

---

## 🎯 Key Files to Review

| Task | File to Check |
|------|--------------|
| AI logic | `app/Services/DocumentValidationService.php` |
| Config | `config/ai.php` |
| Database | `app/Models/DocumentValidation.php` |
| Registration | `app/Http/Controllers/Auth/RegisterController.php` |
| Profile update | `app/Http/Controllers/ProfileController.php` |
| Environment | `.env` |

---

## 📈 Monitoring Queries

```php
// Success rate today
$total = DocumentValidation::whereDate('created_at', today())->count();
$approved = DocumentValidation::whereDate('created_at', today())
    ->approved()->count();
$rate = ($approved / $total) * 100;

// Average confidence score
$avgConfidence = DocumentValidation::avg('confidence_score');

// Pending reviews count
$pending = DocumentValidation::pendingReview()->count();

// Recent rejections with reasons
$rejections = DocumentValidation::rejected()
    ->latest()
    ->limit(10)
    ->get(['reason', 'created_at']);
```

---

## ⚡ Performance Tips

1. **Compress images** before upload (client-side)
2. **Set max file size** to 2MB instead of 5MB
3. **Use queue jobs** for validation (async processing)
4. **Cache validation results** by file hash
5. **Batch process** during off-peak hours

---

## 🔄 Version Info

**Current Version:** 1.0.0
**Last Updated:** November 3, 2025
**Laravel Version:** 12.35.0
**OpenAI Package:** openai-php/client v0.18.0
**Required Model:** GPT-4o (gpt-4-vision-preview also works)

---

**Need more details?** See `DOCUMENT_VALIDATION_GUIDE.md` for full documentation.
