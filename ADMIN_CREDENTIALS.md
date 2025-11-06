# System Administrator Account

## Admin Login Credentials

**Email:** `admin@jobrecommendation.ph`  
**Password:** `admin123`

---

## Account Details

- **User ID:** 3
- **First Name:** System
- **Last Name:** Administrator
- **User Type:** employer (with `is_admin = 1` flag)
- **Company Name:** System Administration

---

## Current User Accounts

### Employers (Regular Users)

1. **Margarita Mondero** (ID: 1)
   - Email: `alexsandra.duhac2002@gmail.com`
   - Company: Margie Store
   - Admin: No

2. **Alex Duhac** (ID: 2)
   - Email: `duhacalexsandra2002@gmail.com`
   - Company: Margie Store
   - Admin: No

### System Administrator (ID: 3)

- **Email:** `admin@jobrecommendation.ph`
- **Password:** `admin123`
- **Access:** Full admin panel access
- **Permissions:**
  - Business permit verification (approve/reject)
  - Admin notifications management
  - User management
  - System oversight

---

## Admin Access URLs

- **Admin Verifications:** `http://127.0.0.1:8000/admin/verifications`
- **Admin Notifications:** `http://127.0.0.1:8000/admin/notifications`

---

## Security Notes

⚠️ **IMPORTANT:** Change the admin password immediately in production!

To change the admin password:
```bash
php artisan tinker
```

Then run:
```php
DB::table('users')->where('email', 'admin@jobrecommendation.ph')->update([
    'password' => bcrypt('your-new-secure-password')
]);
```

---

## Admin Middleware

The admin routes are protected by:
- `auth` middleware (must be logged in)
- `admin` middleware (checks `is_admin = 1`)

**Middleware Location:** `app/Http/Middleware/AdminMiddleware.php`

---

## Notes

- The `user_type` enum only supports `job_seeker` and `employer`
- Admin status is determined by the `is_admin` boolean field (1 = admin, 0 = regular user)
- Admins can have `user_type = employer` but still access admin features via `is_admin = 1`
- Regular employers like `alexsandra.duhac2002@gmail.com` have been fixed to `is_admin = 0`

---

**Last Updated:** November 5, 2025
