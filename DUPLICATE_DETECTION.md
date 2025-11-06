# 🛡️ Duplicate Business Permit Detection System

## Overview
A comprehensive duplicate detection system that prevents multiple accounts from using the same business permit. Uses **combined file hash + company name validation** for maximum accuracy.

---

## ✅ Features Implemented

### 1. **File Hash Detection**
- Calculates SHA-256 hash of uploaded permit files
- Stores hash in `document_validations.file_hash` column (indexed for fast lookup)
- Detects exact file duplicates (same PDF/image uploaded multiple times)

### 2. **Company Name Detection**
- Checks if company name already exists in approved permits
- Prevents duplicate registrations under same business name
- Allows admin override for legitimate cases (branches, renewals)

### 3. **Combined Detection Logic**

| Scenario | File Hash Match | Company Name Match | Action |
|----------|----------------|-------------------|---------|
| **Exact Duplicate** | ✅ Yes | ✅ Yes | 🚫 Flag for review (highly suspicious) |
| **Same File, Different Name** | ✅ Yes | ❌ No | 🚫 Flag for review (possible fraud) |
| **Same Company, Different File** | ❌ No | ✅ Yes | ⚠️ Flag for review (might be renewal/branch) |
| **Unique Permit** | ❌ No | ❌ No | ✅ Proceed with AI validation |

---

## 🔧 How It Works

### **Step 1: Upload**
Employer uploads business permit → System calculates file hash

### **Step 2: Duplicate Check**
```php
// Runs BEFORE AI validation
$duplicateCheck = $this->checkForDuplicatePermit($user);

if ($duplicateCheck['is_duplicate']) {
    // Flag for manual review
    // Send notification to user
    // Store duplicate detection details
}
```

### **Step 3: Admin Review**
- Duplicate flagged as `pending_review`
- Admin sees duplicate detection details in AI analysis
- Admin can:
  - ✅ **Approve** (legitimate branch/renewal)
  - ❌ **Reject** (actual duplicate/fraud)

---

## 📊 Detection Details Stored

When a duplicate is detected, the system stores:

```json
{
  "duplicate_detection": {
    "duplicate_type": "both|file_hash|company_name",
    "file_hash_match": true,
    "company_name_match": true,
    "file_hash": "5904d987f22395d49...",
    "existing_user_email": "original@account.com",
    "existing_validation_id": 123
  },
  "requires_admin_review": true
}
```

---

## 🎯 User Experience

### **Duplicate Detected:**
1. User uploads permit
2. System detects duplicate
3. Status set to: `pending_review`
4. Notification sent:
   > ⚠️ **Business Permit Requires Review**
   > 
   > Your business permit has been flagged for manual review. Our system detected that this business permit is already registered to another account. If this is a mistake, please contact support.

### **Email Notification:**
User receives standard validation email showing status as "pending review"

---

## 🔍 Current Status

### **Your Accounts:**

**Account 1 (Original):**
- Email: `alexsandra.duhac2002@gmail.com`
- Company: Margie Store
- Permit: ✅ Approved
- File Hash: `5904d987f22395d49277d2ed5d0ac01613d690a1650570011df03b9cc17364e8`

**Account 2 (Test):**
- Email: `duhacalexsandra2002@gmail.com`
- Company: Margie Store
- Permit: ⏳ Not uploaded yet

**If you upload the same permit to Account 2:**
- ✅ File hash will match → Duplicate detected
- ✅ Company name matches → Duplicate confirmed
- 🚫 Status: `pending_review`
- 📧 You'll receive notification about manual review required
- 👤 Admin can approve/reject in admin panel

---

## 📝 Database Changes

### New Column: `file_hash`
```sql
ALTER TABLE document_validations 
ADD COLUMN file_hash VARCHAR(64) NULL AFTER file_path,
ADD INDEX idx_file_hash (file_hash);
```

### Model Updated:
`app/Models/DocumentValidation.php`
- Added `file_hash` to `$fillable`
- Automatically stored on all new validations

---

## 🚀 Benefits

1. **Prevents Fraud** - Stops multiple accounts using same permit
2. **Fast Detection** - Indexed hash lookup (milliseconds)
3. **Admin Control** - Legitimate cases can be approved manually
4. **Transparent** - Users know why they're flagged
5. **Audit Trail** - Full duplicate detection details logged

---

## 🧪 Testing

Run the test script to see detection in action:
```bash
php scripts/test_duplicate_detection.php
```

This will show:
- Current account status
- File hashes calculated
- What would happen if duplicate uploaded

---

## ⚙️ Admin Override

Admins can approve duplicates for legitimate cases:
- **Branch offices** - Same company, different locations
- **Renewed permits** - New permit file, same company
- **Corrected uploads** - User re-uploaded clearer image

Simply click **Approve** in admin panel to override duplicate flag.

---

## 🔐 Security Notes

- SHA-256 hash ensures even 1-byte file difference is detected
- Database index prevents performance degradation
- System-level validation runs before AI (saves API costs)
- All duplicate attempts logged for audit

---

**System is now production-ready with comprehensive duplicate prevention!** 🎉
