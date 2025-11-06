# Personal Email User Experience

## Overview

Employers using personal email addresses (Gmail, Yahoo, Hotmail, Outlook, Live, AOL, iCloud) receive **additional UI notices** and are subject to **stricter verification standards** to maintain platform credibility.

---

## Visual Indicators Added

### 1. **Dashboard Notice (Before Approval)**

**Location:** Employer Dashboard (top section)  
**Visibility:** Shows when using personal email AND permit not yet approved

**Appearance:**
```
┌──────────────────────────────────────────────────────────────────────┐
│ 💼 Using a Personal Email Address                                   │
│                                                                      │
│ You're using duhacalexsandra2002@gmail.com. Personal emails are     │
│ subject to stricter verification standards (90% confidence vs.      │
│ 80%). Consider using a business email (e.g., contact@yourcompany.   │
│ com) for faster approval and improved credibility with job seekers. │
└──────────────────────────────────────────────────────────────────────┘
```

**Design:**
- **Background:** Purple gradient (#667eea → #764ba2)
- **Text:** White, clean, professional
- **Icon:** Info circle (fas fa-info-circle)
- **Style:** Friendly advice, not alarming

**Purpose:**
- Encourages upgrading to business email
- Sets expectations for stricter review
- Improves employer credibility perception

---

### 2. **Settings Page - Business Permit Upload Notice**

**Location:** Settings Page (Business Permit field)  
**Visibility:** Always shows when using personal email

**Appearance:**
```
Business Permit (PDF/JPG/PNG)

┌──────────────────────────────────────────────────────────────────┐
│ 🛡️ Higher Verification Standards: Personal email addresses      │
│ (duhacalexsandra2002@gmail.com) require 90% AI confidence vs.   │
│ 80% for business emails. Upload a clear, high-quality permit to │
│ avoid manual review delays.                                      │
└──────────────────────────────────────────────────────────────────┘

[Choose File] No file chosen

Current: View file

┌──────────────────────────────────────────────────────────────────┐
│ ℹ️ Policy: Each account is tied to one verified business permit  │
│ only. The business name on your permit must match your          │
│ registered company name. To operate multiple businesses,        │
│ register separate employer accounts with separate permits.      │
└──────────────────────────────────────────────────────────────────┘
```

**Design:**
- **Verification Notice:** Purple gradient (#667eea → #764ba2), white text
- **Policy Notice:** Light blue (#e8f0f7), dark text
- **Icons:** Shield (verification), Info circle (policy)

**Purpose:**
- Sets clear expectations before upload
- Encourages uploading high-quality documents
- Explains one-permit-per-account policy

---

## Detection Logic

### Personal Email Detection

```php
@php
  $isPersonalEmail = preg_match('/@(gmail|yahoo|hotmail|outlook|live|aol|icloud)\./i', $user->email);
@endphp
```

**Detected Domains:**
- `@gmail.com` / `@gmail.*`
- `@yahoo.com` / `@yahoo.*`
- `@hotmail.com` / `@hotmail.*`
- `@outlook.com` / `@outlook.*`
- `@live.com` / `@live.*`
- `@aol.com` / `@aol.*`
- `@icloud.com` / `@icloud.*`

**Not Detected (Business Emails):**
- `@company.com`
- `@business.ph`
- `@store.co`
- Custom domain emails

---

## Backend Enforcement

### AI Validation Threshold

**From `.env`:**
```env
AI_MIN_CONFIDENCE_SCORE=80                # Business emails: 80%
AI_MIN_CONFIDENCE_PERSONAL_EMAIL=90       # Personal emails: 90%
```

**From `ValidateBusinessPermitJob.php`:**
```php
$isPersonalEmail = $this->metadata['is_personal_email'] ?? false;
$minConfidenceRequired = $isPersonalEmail
    ? config('ai.document_validation.business_permit.personal_email_min_confidence', 90)
    : config('ai.document_validation.business_permit.min_confidence', 80);

if ($isPersonalEmail && $confidenceScore < $minConfidenceRequired) {
    $validationResult['valid'] = false;
    $validationResult['requires_review'] = true;
    $validationResult['reason'] = 'Personal email detected. Higher verification standards applied. '
                                  .$validationResult['reason'];
}
```

**What This Means:**

| Email Type      | Confidence Required | Auto-Approve Threshold | Likely Outcome                  |
|-----------------|---------------------|------------------------|---------------------------------|
| Business Email  | 80%                 | ≥85%                   | Fast auto-approval              |
| Personal Email  | 90%                 | ≥90%                   | More likely manual review       |

---

## User Flow Examples

### Example 1: Personal Email - High Quality Permit

**User:** `duhacalexsandra2002@gmail.com`

1. **Dashboard shows:**
   - Purple banner: *"Using a Personal Email Address... consider business email"*
2. **Navigates to Settings**
3. **Sees:**
   - Purple notice: *"Higher Verification Standards: 90% AI confidence required"*
   - Blue notice: *"One permit per account policy"*
4. **Uploads:** Crystal-clear DTI certificate PDF with:
   - Official DTI seal
   - Business name: "Duhac Sari-Sari Store"
   - Valid dates, registration number
   - No blur or damage
5. **AI Analysis:**
   - Confidence: **92%** (exceeds 90% threshold)
   - Business name matches: ✅
   - Official seals: ✅
6. **Result:** **AUTO-APPROVED** ✅
7. **Dashboard banner disappears** (approval achieved)

---

### Example 2: Personal Email - Borderline Quality Permit

**User:** `johndoe123@yahoo.com`

1. **Dashboard shows purple banner**
2. **Uploads:** Slightly blurry barangay clearance photo:
   - Barangay seal visible but faint
   - Business name readable
   - Some shadows/lighting issues
3. **AI Analysis:**
   - Confidence: **84%** (below 90% threshold, but above 80%)
   - Business name matches: ✅
   - Appears authentic: ✅
4. **Result:** **PENDING_REVIEW** ⚠️
   - Reason: *"Personal email detected. Higher verification standards applied. Document quality borderline—manual review required."*
5. **User receives notification:**
   - *"Your business permit is under review. You will be notified when approved (typically 24-48 hours)."*
6. **Admin reviews and approves**
7. **Dashboard banner disappears**

---

### Example 3: Personal Email - Poor Quality Upload

**User:** `smallbiz@hotmail.com`

1. **Dashboard shows purple banner**
2. **Ignores notice, uploads:** Blurry phone photo of permit:
   - Text barely readable
   - No clear seals
   - Dark lighting
3. **AI Analysis:**
   - Confidence: **62%** (far below 90% threshold)
   - Business name matches: Uncertain
   - Appears authentic: Uncertain
4. **Result:** **PENDING_REVIEW** (or rejected if <50%)
   - Reason: *"Personal email detected. Higher verification standards applied. Document quality too low for automatic verification."*
5. **Admin reviews:**
   - Sends rejection: *"Please re-upload a clear, high-resolution scan or photo of your business permit."*
6. **User sees dashboard alert:**
   - Red banner: *"Business Permit Verification Failed"*
   - Can re-upload via modal

**If they had seen the purple notice and uploaded a clear scan initially:**
- Would have been **auto-approved** (if quality ≥90% confidence)
- Saved 24-48 hours of waiting

---

## Benefits of These Notices

### ✅ **For Employers**
- **Clear expectations** before uploading
- **Faster approval** if they follow guidance
- **Understand why** stricter standards apply
- **Encouraged to upgrade** to business email for better credibility

### ✅ **For Platform**
- **Higher quality** permit uploads from personal email users
- **Fewer rejections** due to poor quality
- **Better fraud prevention** (personal emails often used for quick signups)
- **Professional image** maintained

### ✅ **For Admins**
- **Fewer manual reviews** needed (better quality uploads)
- **Faster processing** when reviews are needed (clearer documents)
- **Documented policy** reduces disputes

---

## Configuration Options

Admins can adjust thresholds in `.env`:

```env
# Lower threshold = easier approval (less strict)
# Higher threshold = harder approval (more strict)

AI_MIN_CONFIDENCE_SCORE=80                # For business emails (default: 80)
AI_MIN_CONFIDENCE_PERSONAL_EMAIL=90       # For personal emails (default: 90)
AI_AUTO_APPROVE_THRESHOLD=85              # Auto-approve if ≥ this (default: 85)
AI_AUTO_REJECT_THRESHOLD=50               # Auto-reject if < this (default: 50)
```

**Recommended Settings:**
- **Standard Platform:** 80% / 90% (current)
- **Strict Verification:** 85% / 95%
- **Lenient/Startup:** 70% / 80%

---

## Testing Checklist

- [ ] Personal email users see purple banner on dashboard (before approval)
- [ ] Banner disappears after approval
- [ ] Settings page shows verification notice for personal emails
- [ ] Settings page shows one-permit policy notice for all users
- [ ] Business email users do NOT see purple banners
- [ ] Regex correctly detects Gmail/Yahoo/Hotmail/Outlook/Live/AOL/iCloud
- [ ] Regex does NOT detect custom business domains
- [ ] AI applies 90% threshold to personal emails
- [ ] AI applies 80% threshold to business emails
- [ ] Notices are responsive and display correctly on mobile
- [ ] Text is clear, friendly, and actionable

---

## Summary

**Personal email employers (`duhacalexsandra2002@gmail.com`, etc.) now see:**

1. **Dashboard Purple Banner:**
   - *"Using personal email → 90% confidence required"*
   - Encourages business email upgrade
   - Only shows before approval

2. **Settings Verification Notice:**
   - *"Upload high-quality permit to avoid delays"*
   - Sets expectations clearly
   - Always visible for personal emails

3. **Settings Policy Notice:**
   - *"One permit per account"*
   - Explains business name matching
   - Visible for all employers

**Result:**
- ✅ Better quality uploads from personal email users
- ✅ Fewer manual reviews needed
- ✅ Faster approval times overall
- ✅ Clear understanding of platform policies
- ✅ Encouraged to upgrade to professional business emails

---

**Implementation Complete!** 🎉
