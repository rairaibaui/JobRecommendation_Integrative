# Admin Manual Approval Guide

## How to Access Admin Verification Panel

### Step 1: Login as Admin
1. Navigate to: `http://localhost:8000/login`
2. Login with an admin account (user with `is_admin = 1` in the database)

### Step 2: Access Verification Dashboard
- URL: `http://localhost:8000/admin/verifications`
- Or click on "Admin Panel" > "Verifications" in the navigation

---

## Verification Dashboard Overview

You'll see three statistics cards:
- **Pending Review**: Number of permits waiting for approval
- **Approved**: Total approved permits
- **Rejected**: Total rejected permits

---

## How to Approve a Duplicate Permit

When you see a pending verification for `duhacalexsandra2002@gmail.com`:

### 1. View the Permit Document
- Click the **"View"** button to open the business permit in a new tab
- Examine the document for:
  - ✅ DTI logo and official seals
  - ✅ Business Name Number: `6940949`
  - ✅ Company Name: MARGARITA SARI-SARI STORE
  - ✅ Owner: MARGARITA PALLES MONDERO
  - ✅ Valid until: Feb 21, 2030
  - ✅ Location: Addition Hills, Mandaluyong

### 2. Check Duplicate Detection Details
The system flagged this as duplicate because:
- **File Hash Match**: YES (same exact file)
- **Company Name Match**: YES (both accounts use "Margie Store")
- **Original Account**: alexsandra.duhac2002@gmail.com

### 3. Make a Decision

#### ✅ To APPROVE (if legitimate):
Reasons to approve:
- Branch office of the same company
- Renewed permit for the same business
- Multiple managers/HR staff for same company
- Test account (like in this case)

**Action:**
1. Click the green **"Approve"** button
2. Confirm the approval
3. The system will:
   - Set validation status to `approved`
   - Set `is_valid = true`
   - Set `confidence_score = 100`
   - Send email notification to employer
   - Create in-app notification: "Business Permit Approved! You can now post job listings."
   - Allow the employer to post jobs immediately

#### ❌ To REJECT (if fraudulent):
Reasons to reject:
- Duplicate account trying to scam the system
- Same person creating multiple accounts
- Actual fraud attempt

**Action:**
1. Click the red **"Reject"** button
2. A modal will appear asking for rejection reason
3. Enter detailed reason, for example:
   - "Duplicate account detected. This permit is already registered to alexsandra.duhac2002@gmail.com"
   - "Same business permit cannot be used for multiple accounts"
   - "Please contact support if this is a legitimate branch office"
4. Click **"Reject"**
5. The system will:
   - Set validation status to `rejected`
   - Set `is_valid = false`
   - Send email notification with rejection reason
   - Create in-app notification with the rejection reason
   - Continue blocking the employer from posting jobs

---

## Advanced: Approve with Expiry Date

When approving, you can optionally set the permit expiry date:

1. The approve form accepts `permit_expiry_date` parameter
2. For this permit, you should set: **2030-02-21** (Feb 21, 2030)
3. The system will:
   - Store the expiry date in `document_validations.permit_expiry_date`
   - Send reminder emails 30 days before expiration
   - Automatically flag expired permits for re-review

> **Note**: The current UI doesn't show this field yet. You can add it or use the database directly.

---

## Current Test Scenario

### Account 1: `alexsandra.duhac2002@gmail.com`
- Status: `pending_review` (from a revalidation test)
- Original approved validation exists (ID: 1)
- Should be re-approved

### Account 2: `duhacalexsandra2002@gmail.com`
- Status: `pending_review` (duplicate detected)
- Reason: "This business permit file and company name are already registered to another account"
- **Validation ID: 5**

### Recommended Action for Testing
Since these are both test accounts for the same person:
- **APPROVE** both accounts
- Or **DELETE** Account 2 if it was just for testing duplicate detection

---

## Quick Commands for Testing

### Check Pending Verifications:
```bash
php scripts/check_validation_status.php
```

### View All Employers:
```bash
php scripts/list_employers.php
```

### Check Duplicate Detection Results:
```bash
php scripts/check_duplicate_result.php
```

### Manually Approve via Script (bypass UI):
```bash
php scripts/reapprove_employer.php duhacalexsandra2002@gmail.com
```

---

## What Happens After Approval

1. **Email Notification**: Employer receives "Business Permit Approved" email
2. **In-App Notification**: Success notification with green check icon
3. **Job Posting Unlocked**: Employer can immediately create job postings
4. **Verification Badge**: Employer dashboard shows green "Verified" badge
5. **Dashboard Access**: Full access to all employer features

---

## Database Records

After approval, the `document_validations` table will show:
```
validation_status: approved
is_valid: true
confidence_score: 100
validated_by: admin
validated_at: [current timestamp]
permit_expiry_date: 2030-02-21 (if set)
```

---

## Future Enhancements

- [ ] Add expiry date picker in the approval UI
- [ ] Show duplicate detection details in the verification table
- [ ] Display permit_number in the admin panel
- [ ] Add bulk approve/reject actions
- [ ] Export verification history to CSV
