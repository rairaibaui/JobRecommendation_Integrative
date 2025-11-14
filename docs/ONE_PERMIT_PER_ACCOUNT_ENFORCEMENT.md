# One Permit Per Account Enforcement System

## Overview

This document details how the system enforces **one verified business permit per employer account** to ensure proper legal compliance and prevent misuse.

---

## Core Principle

**Each employer account is strictly tied to a single verified business permit.** Employers cannot:
- Post jobs for a different business name than the one on their approved permit
- Change their company name after approval unless uploading a new permit
- Use a permit that doesn't match their registered business name

If an employer wants to operate **multiple businesses**, they must register **separate employer accounts** with **separate business permits**.

---

## Implementation Points

### 1. **AI-Powered Validation (ValidateBusinessPermitJob)**

**Location:** `app/Jobs/ValidateBusinessPermitJob.php`

The AI validation service checks if the business name on the permit matches the registered `company_name`:

```php
// Enforce business-name match. If AI indicates a mismatch with the registered company name,
// do not auto-approve; force manual review and show a clear reason.
$aiAnalysis = $validationResult['ai_analysis'] ?? [];
if (array_key_exists('business_name_matches', $aiAnalysis) && $aiAnalysis['business_name_matches'] === false) {
    $validationResult['valid'] = false;
    $validationResult['requires_review'] = true;
    $validationResult['reason'] = "Business name on the permit doesn't match your registered company name. Each account is tied to one business permit only.";
}
```

**What happens:**
- The AI checks if `business_name_matches` is explicitly `false`
- If mismatch detected → automatic rejection is prevented
- Status set to `pending_review` with a clear reason
- Admin must manually review and decide

**AI Prompt includes:**
```
6. Does the business name on the document reasonably match or relate to '{$companyName}'?
```

And in the response:
```json
"business_name_matches": true/false/null
```

---

### 2. **Admin Approval Stores Approved Company Name**

**Location:** `app/Http/Controllers/Admin/VerificationController.php`

When an admin approves a permit, the system captures the company name at approval time:

```php
// Capture the business name snapshot that this approval is tied to
$aiAnalysis['approved_company_name'] = $user->company_name;
$validation->ai_analysis = $aiAnalysis;
```

**What happens:**
- The `approved_company_name` is stored in the `ai_analysis` JSON column
- This creates an immutable record of which business name the permit was approved for
- Used later to enforce job posting and profile update restrictions

**Also ensures only one approved permit per account:**
```php
// Ensure only one approved permit is active per account: supersede older approvals
DocumentValidation::where('user_id', $user->id)
    ->where('document_type', 'business_permit')
    ->where('validation_status', 'approved')
    ->where('id', '!=', $validation->id)
    ->update([
        'validation_status' => 'rejected',
        'is_valid' => false,
        'reason' => 'Superseded by a newer approved business permit (ID '.$validation->id.').',
    ]);
```

---

### 3. **Job Posting Restriction**

**Location:** `app/Http/Controllers/JobPostingController.php`

Before allowing job posting, the system checks:

```php
// Enforce posting only for the approved business name
$approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
if ($approvedCompanyName && $user->company_name !== $approvedCompanyName) {
    return redirect()->route('employer.dashboard')->withErrors([
        'validation' => "Your account is approved for '{$approvedCompanyName}'. You cannot post jobs under a different business name. To operate another business, register a separate employer account with its own permit.",
    ]);
}
```

**What happens:**
- Every job posting attempt is gated by the `approved_company_name`
- If current `company_name` doesn't match `approved_company_name` → **blocked**
- Clear error message directs employers to register a new account for other businesses

**Also applied in `store()` method with same logic.**

---

### 4. **Company Name Change Restriction (Profile Settings)**

**Location:** `app/Http/Controllers/ProfileController.php`

Employers cannot change their company name if they have an approved permit without uploading a new one:

```php
// Enforce one-permit-per-account: if there is an approved permit, prevent changing the company name
$existingApprovedValidation = \App\Models\DocumentValidation::where('user_id', $user->id)
    ->where('document_type', 'business_permit')
    ->where('validation_status', 'approved')
    ->orderByDesc('created_at')
    ->first();

if ($existingApprovedValidation && !$request->hasFile('business_permit')) {
    // They are NOT uploading a new permit—block company name change
    $approvedCompanyName = $existingApprovedValidation->ai_analysis['approved_company_name'] ?? null;
    if ($approvedCompanyName && $request->company_name !== $approvedCompanyName) {
        return back()->withErrors([
            'company_name' => "Your verified business permit is tied to '{$approvedCompanyName}'. You cannot change your business name unless you upload a new permit. To operate a different business, register a separate employer account.",
        ]);
    }
}
```

**What happens:**
- If employer tries to change `company_name` in Settings → system checks for approved permit
- If approved permit exists and no new permit uploaded → **name change blocked**
- Employer must upload a new permit if business name actually changed (e.g., renewal or legal name update)

---

### 5. **Employer Dashboard UI Feedback**

**Location:** `resources/views/employer/dashboard.blade.php`

The dashboard shows clear visual warnings when there is a business name mismatch:

```php
@php
  // Enforce one permit per account: check if the approved company name is locked
  $approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
  $companyNameMismatch = ($approvedCompanyName && $user->company_name !== $approvedCompanyName);
@endphp

@if($companyNameMismatch)
  <div style="background: #fff3cd; color: #856404; ...">
    <strong>⚠️ Business Name Mismatch</strong>
    <p>Your verified business permit is registered to <strong>{{ $approvedCompanyName }}</strong>, 
       but your current Company Name is <strong>{{ $user->company_name }}</strong>.</p>
    <p><strong>Policy:</strong> Each employer account is tied to <strong>one verified business permit only</strong> 
       for legal compliance. You cannot post jobs or change your business name until this is resolved.</p>
    <p><strong>To fix this:</strong></p>
    <ul>
      <li>Revert your company name back to <strong>{{ $approvedCompanyName }}</strong> in Settings, or</li>
      <li>If you are operating a <em>different business</em>, please <strong>register a new employer account</strong> 
          with a valid permit for that business.</li>
    </ul>
  </div>
@endif
```

**Visual feedback:**
- Yellow warning banner when mismatch detected
- Explains the one-permit policy
- Provides actionable steps to resolve

**Job posting button is also disabled:**
```php
$canPostJobs = $validation && $validation->validation_status === 'approved';
// Also block if company name mismatch
if ($canPostJobs) {
  $approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
  if ($approvedCompanyName && $user->company_name !== $approvedCompanyName) {
    $canPostJobs = false;
  }
}
```

**"Post New Job" button shows as disabled/locked:**
```html
<a href="#" class="btn-primary" style="...cursor:not-allowed;" 
   title="You cannot post jobs until your business permit is approved." 
   onclick="return false;">
  <i class="fas fa-lock"></i>
  Post New Job
</a>
```

**On successful approval, policy reminder is shown:**
```php
<p style="margin: 8px 0 0 0; font-size: 13px; opacity: 0.9;">
  <strong>Note:</strong> This account is tied to <strong>one business permit only</strong>. 
  If you want to operate a different business, register a new employer account with a separate permit.
</p>
```

---

## User Flow Examples

### Example 1: Employer Tries to Change Business Name After Approval

**Scenario:** "ABC Bakery" is approved → employer tries to change name to "XYZ Restaurant"

1. Employer navigates to Settings
2. Changes `company_name` from "ABC Bakery" to "XYZ Restaurant"
3. Clicks "Save Settings"
4. **System blocks** the update with error:
   > Your verified business permit is tied to 'ABC Bakery'. You cannot change your business name unless you upload a new permit. To operate a different business, register a separate employer account.

**Result:** Name change blocked, profile unchanged.

---

### Example 2: Employer Uploads Permit for Different Business

**Scenario:** "ABC Bakery" employer uploads permit for "XYZ Sari-Sari Store"

1. Employer uploads new permit PDF
2. AI validates the document
3. **AI detects:** Business name on permit = "XYZ Sari-Sari Store", Registered name = "ABC Bakery"
4. AI sets `business_name_matches: false`
5. Validation job forces `pending_review` with reason:
   > Business name on the permit doesn't match your registered company name. Each account is tied to one business permit only.
6. Admin receives notification to review
7. Admin sees the mismatch and can:
   - **Reject** with message: "This permit is for a different business. Please register a new account for XYZ Sari-Sari Store."
   - **Approve (override)** if it's a legitimate case (e.g., DBA/branch)

**Result:** Mismatch flagged for manual review, not auto-approved.

---

### Example 3: Employer Tries to Post Job for Different Business

**Scenario:** "ABC Bakery" is approved → employer changes name to "XYZ Restaurant" and tries to post job

1. Employer somehow changes `company_name` to "XYZ Restaurant" (e.g., direct DB update or bug)
2. Dashboard shows **yellow warning banner:**
   > ⚠️ Business Name Mismatch  
   > Your verified business permit is registered to **ABC Bakery**, but your current Company Name is **XYZ Restaurant**.  
   > You cannot post jobs or change your business name until this is resolved.
3. "Post New Job" button is **disabled/locked**
4. If employer bypasses UI and sends POST request to create job:
   - Controller checks `approved_company_name` vs current `company_name`
   - Request **blocked** with error:
     > Your account is approved for 'ABC Bakery'. You cannot post jobs under a different business name. To operate another business, register a separate employer account with its own permit.

**Result:** Job posting completely blocked until resolved.

---

## Admin Workflow

### Reviewing Business Name Mismatch

When admin receives a `pending_review` with mismatch:

1. **Admin Notifications Page** shows: "New Business Permit Uploaded" or "Employer Re-uploaded Permit"
2. Admin clicks "View Details"
3. **Verification Detail Page** displays:
   - **Registered Company Name:** ABC Bakery
   - **AI Analysis:**
     ```json
     "business_name_matches": false,
     "issuing_authority": "DTI Manila",
     "business_name_on_permit": "XYZ Sari-Sari Store"
     ```
   - **Reason:** Business name on the permit doesn't match your registered company name...
4. Admin options:
   - **Reject:** "This permit is for a different business. Register a new account for XYZ."
   - **Approve (Override):** If legitimate (e.g., branch office, DBA, renewal with new name)
5. On approval, `approved_company_name` is set to current `user->company_name`
6. Employer is notified and can now operate under that name only

---

## Database Schema

### `document_validations.ai_analysis` JSON Column

Sample approved record:
```json
{
  "document_type": "DTI Business Name Registration",
  "has_official_seals": true,
  "business_name_matches": true,
  "permit_number": "123456789",
  "issuing_authority": "DTI Manila",
  "validity_dates": "Valid until: December 31, 2025",
  "admin_approval": {
    "admin_id": 1,
    "admin_email": "admin@example.com",
    "approved_at": "2025-11-05 10:30:00",
    "notes": "Valid DTI registration, verified via official registry",
    "duplicate_override": false
  },
  "approved_company_name": "ABC Bakery"
}
```

**Key field:** `approved_company_name` — immutable snapshot of business name at approval time.

---

## Configuration

No special config needed. The enforcement is always active and uses:
- `User.company_name` — current registered business name
- `DocumentValidation.ai_analysis['approved_company_name']` — approved name at permit verification
- AI prompt includes business name matching check
- Controller guards on job posting and profile updates

---

## Benefits

### ✅ Legal Compliance
- Ensures permits are tied to actual businesses
- Prevents fraud (employer using someone else's permit)
- Creates audit trail for each business operation

### ✅ Clear User Guidance
- Dashboard warnings explain policy clearly
- Error messages direct users to register new accounts for new businesses
- No silent failures or confusing restrictions

### ✅ Admin Control
- AI flags mismatches automatically
- Admin can override for legitimate cases
- Audit trail logs all actions

### ✅ Prevents Common Misuses
- **Scenario A:** Employer tries to post jobs for multiple unrelated businesses → blocked
- **Scenario B:** Employer uploads wrong permit → flagged for review
- **Scenario C:** Employer changes business name after approval → blocked unless new permit uploaded

---

## Testing Checklist

- [ ] AI detects business name mismatch and forces `pending_review`
- [ ] Admin approval stores `approved_company_name` in `ai_analysis`
- [ ] Only one approved permit can exist per account (supersedes old)
- [ ] Job posting blocked when `company_name ≠ approved_company_name`
- [ ] Company name change blocked unless new permit uploaded
- [ ] Dashboard shows warning banner on mismatch
- [ ] "Post New Job" button disabled when mismatch or not approved
- [ ] Profile update endpoint validates against approved name
- [ ] Employer re-upload resets to `pending_review` and notifies admins
- [ ] Clear error messages guide employers to register separate accounts

---

## Future Enhancements

1. **Multi-Branch Support:**
   - Allow single account to manage multiple branches
   - Require branch permit for each location
   - Associate job postings with specific branches

2. **Business Name Change Request Flow:**
   - Employer submits name change request with supporting docs
   - Admin reviews and approves/rejects
   - If approved, updates `approved_company_name`

3. **Automated Permit Renewal Reminders:**
   - Email 30/15/7 days before expiry
   - Disable job posting when expired
   - Force re-upload when expired

4. **RBAC for Admin Actions:**
   - Different permission levels (reviewer, approver, super admin)
   - Audit trail for all admin decisions
   - Two-person approval for overrides

---

## Summary

The one-permit-per-account enforcement is implemented at **multiple layers**:

1. **AI Validation** — Flags business name mismatches
2. **Admin Approval** — Captures approved company name
3. **Job Posting Controller** — Blocks posting for different business
4. **Profile Controller** — Blocks name change without new permit
5. **Dashboard UI** — Shows clear warnings and disables actions
6. **Database** — Stores `approved_company_name` immutably

This ensures **each employer account represents exactly one verified business** with full legal compliance and audit trail.
