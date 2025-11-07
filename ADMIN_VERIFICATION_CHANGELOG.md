# Admin Verification System - Changelog

**Context:** This update enhances the System Administrator dashboard with comprehensive permit lifecycle management, including expiry tracking, admin notification workflows, secure file handling, and improved UI clarity. The changes ensure admins have full visibility into permit status transitions and can enforce re-verification workflows for rejected permits.

---

## ✅ Highlights

- **Expiry Tracking & Alerts**: Added "Expiring Soon" filter, expiry date column, and automated admin notifications for permits nearing expiration or already expired
- **Admin Notification System**: Real-time badge with unread count, modal listing expiring/expired permits, and "Mark All as Read" functionality with instant UI updates
- **Secure File Access**: Replaced direct `/storage` URLs with authorized admin routes to prevent 403 errors and maintain proper access control
- **Approval Workflow Guardrails**: Disabled "Approve" button for rejected permits until employer uploads a new submission, ensuring proper re-verification cycle
- **Enhanced Modal UI**: Confidence score progress bar, validation status icons, muted "N/A" styling, structured admin history, and user guidance notes

---

## ⚙️ Changes Made

### 1. Expiry Management & Filtering

**Controller** (`app/Http/Controllers/Admin/VerificationController.php`)
- Added `status=expiring_soon` filter: queries permits with `permit_expiry_date` between today and 30 days ahead
- Computed statistics: `expiringSoonCount`, `expiredCount`
- Prepared data lists: `expiringSoonList`, `expiredList` (top 50, ordered by expiry)
- Calculated `adminUnreadCount` for notification badge
- Generated `usersWithPending`: array of employer IDs who have an active `pending_review` submission (used to enforce re-verification after rejection)

**View** (`resources/views/admin/verifications.blade.php`)
- Added "Expiring Soon (30 days)" option to status filter dropdown
- Introduced "Expiry Date" table column with formatted dates (e.g., "Nov 5, 2025")
- Implemented dynamic status badges:
  - "Expiring Soon" (yellow) for permits within 30 days of expiry
  - "Expired" (red) for permits past expiry date
- Added optional summary card displaying total permits expiring in next 30 days
- Updated CSS for new badge styles: `.status-expiring`, `.status-expired`

### 2. Admin Notification Workflow

**Scheduled Command** (`app/Console/Commands/CheckExpiredPermits.php`)
- Enhanced `handleExpiredPermit()`: creates error-type admin notifications for all admin users when a permit expires
- Enhanced `sendExpiryReminder()`: creates warning-type admin notifications when permits enter 30-day expiry window
- Implemented de-duplication logic using `data->validation_id` to prevent repeated notifications
- Corrected notification field name from `is_read` to `read` for proper model compatibility

**Controller** (`app/Http/Controllers/Admin/NotificationController.php` - new)
- Created `markAllRead()`: bulk-updates all unread notifications for current admin user
- Returns JSON response with success status

**Routes** (`routes/web.php`)
- Registered `POST /admin/notifications/mark-all-read` (name: `admin.notifications.markAllRead`)
- Protected by `auth` and `admin` middleware

**View** (`resources/views/admin/verifications.blade.php`)
- Replaced permit count badge with admin unread notification badge
- Modal displays two sections: "Expiring Soon" and "Expired" permits with company names, emails, and expiry dates
- Added "Mark All as Read" button with AJAX implementation
- JavaScript function `markAllAdminNotificationsRead()`:
  - Sends POST request with CSRF token
  - On success: hides badge and resets count to 0 without page reload

### 3. Secure Document File Access

**Controller** (`app/Http/Controllers/Admin/VerificationController.php`)
- Implemented `file()` method: streams document files via `response()->file()` with proper MIME types and cache headers
- Validates file existence and permission before serving

**Routes** (`routes/web.php`)
- Added `GET /admin/verifications/{id}/file` (name: `admin.verifications.file`)
- Protected by `auth` and `admin` middleware

**View** (`resources/views/admin/verifications.blade.php`)
- Updated document preview and download URLs from `asset('storage/...')` to `route('admin.verifications.file', $validation->id)`
- Eliminates 403 errors caused by missing storage symlinks or web server ACL restrictions

### 4. Approval Workflow After Rejection

**Purpose:** Ensures employers must upload a new permit before an admin can re-approve after rejection. This enforces a proper re-verification cycle and prevents approval of stale rejected submissions.

**Controller** (`app/Http/Controllers/Admin/VerificationController.php`)
- Computed `usersWithPending`: unique array of employer IDs with active `pending_review` submissions
- Passed to view for conditional rendering logic

**View** (`resources/views/admin/verifications.blade.php`)
- Added logic per row:
  - `$hasPendingForUser`: checks if employer ID exists in `usersWithPending`
  - `$canApprove`: disables approval if current record is rejected AND employer has no new pending submission
- Rendered disabled "Approve" button with tooltip: "Awaiting new permit upload for re-verification."
- CSS: added `.btn[disabled]` styling (opacity 0.6, cursor not-allowed, pointer-events none)

### 5. Email Template Clarity

**Template** (`resources/views/emails/business-permit-validated.blade.php`)
- Clarified badge display logic: "✔ Verified" badge now appears only when `$isApproved` is true
- Prevents confusing mixed messaging on rejection or pending review emails

### 6. Enhanced Business Permit Details Modal

**View** (`resources/views/admin/verifications.blade.php`)

**Visual Improvements:**
- **Confidence Score**: Replaced static badge with animated progress bar
  - Color-coded thresholds: Green (≥80%), Yellow (50–79%), Red (<50%)
  - Numeric percentage label aligned to the right
  - Handles both 0–1 and 0–100 input ranges
- **N/A Styling**: Applied `.muted` class (gray color) to empty or "N/A" field values for improved readability
- **Validation Method Icon**: Dynamic badge with status-appropriate icons
  - ✅ "Verified" (approved status)
  - ❌ "Rejected" (rejected status)
  - 🤖 "AI Validated" (validated_by = ai)
  - ⚠️ "System Flagged" (default fallback)

**Admin Action History Card:**
- Structured layout using `.admin-info` bordered card and `.kv-item` key-value grid
- Aligned labels and values for clarity: Action, Admin ID, Email, Timestamp
- Full-width rows for Notes and Rejection Reason

**User Guidance:**
- Added helper note at modal footer: "If information appears outdated, request a new permit upload."

**CSS Additions:**
- `.progress-wrap`, `.progress`, `.fill.green|yellow|red`, `.progress-label`
- `.muted` for subdued N/A text
- `.admin-info`, `.kv-item` for structured admin history
- `.helper-note` for guidance text

**JavaScript Enhancements:**
- `openDetailModal()`: refactored to compute validation icon dynamically, render progress bar, apply muted styling, and restructure admin history HTML

---

## 🧩 Quality Gates

- **Build/Type Check**: PASS
  - PHP syntax validated
  - Route registration confirmed via `php artisan route:list`
  - Blade templates compiled without errors
  - No breaking changes to existing routes or controller methods

- **Lint**: Not configured in repository (manual review performed)

- **Tests**: No automated tests present for these features (manual QA recommended)

---

## 🧪 Testing / Next Steps

### Manual QA Checklist

Verify the following features in the admin dashboard:

- [ ] **Expiring Soon Filter**: Status dropdown includes "Expiring Soon (30 days)" option; selecting it displays only permits with expiry dates within the next 30 days
- [ ] **Expiry Date Column**: Table shows formatted expiry dates (e.g., "Nov 5, 2025"); "N/A" appears for records without expiry data
- [ ] **Expiry Badges**:
  - [ ] Yellow "Expiring Soon" badge appears for permits expiring within 30 days
  - [ ] Red "Expired" badge appears for permits past their expiry date
- [ ] **Admin Notifications**:
  - [ ] Badge displays unread notification count next to "Business Permit Verifications" heading
  - [ ] Clicking badge opens modal listing expiring and expired permits with company names, emails, and expiry dates
  - [ ] "Mark All as Read" button updates badge count to 0 and hides badge without page reload
- [ ] **Rejected Permit Approval Guard**:
  - [ ] "Approve" button is disabled with tooltip for rejected records when employer has not uploaded a new permit
  - [ ] "Approve" button becomes enabled once employer submits a new `pending_review` record
- [ ] **File Preview**:
  - [ ] "View" modal document preview loads without 403 errors
  - [ ] "Download Original File" link downloads document successfully
  - [ ] URLs use `/admin/verifications/{id}/file` route (not `/storage/...`)
- [ ] **Enhanced Modal UI**:
  - [ ] Confidence score displays as color-coded progress bar (green/yellow/red)
  - [ ] Validation method shows appropriate icon (✅/❌/🤖/⚠️)
  - [ ] Empty fields display "N/A" in muted gray text
  - [ ] Admin Action History renders in bordered card with aligned labels
  - [ ] Helper note appears at modal footer: "If information appears outdated, request a new permit upload."
- [ ] **Email Clarity**:
  - [ ] Approved emails show "✔ Verified" badge
  - [ ] Rejected and pending emails do not show "✔ Verified" badge

### Optional Enhancements

- **Pagination**: Add pagination or infinite scroll for verification table to handle large datasets
- **Admin Notification History Page**: Dedicated page for viewing all admin notifications with filtering and search
- **Request Re-upload Action**: Add button in modal to send admin-triggered notification requesting new permit upload
- **Separate Badges**: Split expiring/expired badge counts (yellow for expiring, red for expired) instead of combined count
- **Signed URLs**: Use time-limited signed URLs for document file access for enhanced security

### Command Reference

Run the daily expiry check manually:
```powershell
php artisan permits:check-expiry
```

Verify route registration:
```powershell
php artisan route:list --name=admin.verifications.file
php artisan route:list --name=admin.notifications.markAllRead
```

Clear application cache after updates:
```powershell
php artisan optimize:clear
```

---

**Summary**: The admin verification system now provides comprehensive permit lifecycle management with proactive expiry monitoring, secure document access, enforced re-verification workflows, and an enhanced user experience through improved visual clarity and real-time notifications.
