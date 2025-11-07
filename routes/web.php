<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ContactSupportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerAnalyticsController;
use App\Http\Controllers\EmployerApplicantsController;
use App\Http\Controllers\EmployerAuditLogController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\EmployerEmployeesController;
use App\Http\Controllers\EmployerHistoryController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\MyApplicationsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\WorkHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and contain the "web" middleware group.
|
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 🔹 Authentication Routes

// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Account Deletion
Route::delete('/account/delete', [App\Http\Controllers\AccountController::class, 'destroy'])->name('account.delete')->middleware('auth');

// Password Reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Contact Support
Route::get('/contact-support', [ContactSupportController::class, 'show'])->name('contact.support');
Route::post('/contact-support', [ContactSupportController::class, 'submit'])->name('contact.submit');

// Debug route to check auth status
Route::get('/debug/auth', [App\Http\Controllers\DebugController::class, 'checkAuth'])->middleware('auth');

// 🔹 Routes that require authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Employer Dashboard
    Route::get('/employer/dashboard', [EmployerDashboardController::class, 'index'])->name('employer.dashboard');
    // Employer Applicants Management
    Route::get('/employer/applicants', [EmployerApplicantsController::class, 'index'])->name('employer.applicants');
    Route::get('/employer/applications/{application}/applicant', [EmployerApplicantsController::class, 'showApplicant'])->name('employer.applicants.show');
    Route::post('/employer/applications/{application}/status', [EmployerApplicantsController::class, 'updateStatus'])->name('employer.applications.updateStatus');
    Route::delete('/employer/applications/{application}', [EmployerApplicantsController::class, 'destroy'])->name('employer.applications.destroy');

    // Employer History (Hired/Rejected Records)
    Route::get('/employer/history', [EmployerHistoryController::class, 'index'])->name('employer.history');
    // Employer Analytics (Statistics & Insights)
    Route::get('/employer/analytics', [EmployerAnalyticsController::class, 'index'])->name('employer.analytics');
    // Employer Audit Logs
    Route::get('/employer/audit-logs', [EmployerAuditLogController::class, 'index'])->name('employer.auditLogs');
    // Employer Employees (Accepted/Hired applicants)
    Route::get('/employer/employees', [EmployerEmployeesController::class, 'index'])->name('employer.employees');
    Route::post('/employer/employees/{user}/terminate', [EmployerEmployeesController::class, 'terminate'])->name('employer.employees.terminate');

    // Employer Job Postings
    Route::get('/employer/jobs', [JobPostingController::class, 'index'])->name('employer.jobs');
    Route::get('/employer/jobs/create', [JobPostingController::class, 'create'])->name('employer.jobs.create');
    Route::post('/employer/jobs', [JobPostingController::class, 'store'])->name('employer.jobs.store');
    Route::get('/employer/jobs/{jobPosting}/edit', [JobPostingController::class, 'edit'])->name('employer.jobs.edit');
    Route::put('/employer/jobs/{jobPosting}', [JobPostingController::class, 'update'])->name('employer.jobs.update');
    Route::patch('/employer/jobs/{jobPosting}/status', [JobPostingController::class, 'updateStatus'])->name('employer.jobs.updateStatus');
    Route::delete('/employer/jobs/{jobPosting}', [JobPostingController::class, 'destroy'])->name('employer.jobs.destroy');

    // Recommendation
    Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation');

    // My Applications
    Route::get('/my-applications', [MyApplicationsController::class, 'index'])->name('my-applications');
    Route::delete('/my-applications/{application}', [MyApplicationsController::class, 'destroy'])->name('my-applications.destroy');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');

    // Work History (Job Seeker)
    Route::get('/work-history', [WorkHistoryController::class, 'index'])->name('work-history');

    // Settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

    // Clear Bookmarks
    Route::post('/clear-bookmarks', [DashboardController::class, 'clearBookmarks'])->name('clear.bookmarks');

    // Change Password
    Route::get('/change-password', [DashboardController::class, 'changePassword'])->name('change.password');
    Route::post('/change-password', [DashboardController::class, 'updatePassword'])->name('change.password.submit');

    // Profile management
    Route::prefix('profile')->group(function () {
        Route::post('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/update-employer', [ProfileController::class, 'updateEmployer'])->name('profile.updateEmployer');
        Route::get('/resume', [ProfileController::class, 'resume'])->name('profile.resume');
        Route::post('/change-email', [ProfileController::class, 'changeEmail'])->name('profile.changeEmail');
        Route::post('/change-phone', [ProfileController::class, 'changePhone'])->name('profile.changePhone');
        Route::post('/send-phone-otp', [ProfileController::class, 'sendPhoneOTP'])->name('profile.sendPhoneOTP');
        Route::post('/verify-phone-otp', [ProfileController::class, 'verifyPhoneOTP'])->name('profile.verifyPhoneOTP');
        Route::post('/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');

        // Employment actions (job seeker resign)
        Route::post('/resign', [ProfileController::class, 'resign'])->name('profile.resign');

        // Permanently delete account
        Route::delete('/delete', [ProfileController::class, 'destroyAccount'])->name('profile.delete');

        // Profile picture upload
        Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.uploadPhoto')->middleware('auth');
        Route::post('/profile/remove-photo', [ProfileController::class, 'removePhoto'])->name('profile.removePhoto')->middleware('auth');
    });

    // Bookmark management
    Route::prefix('bookmark')->group(function () {
        Route::post('/add', [BookmarkController::class, 'store'])->name('bookmark.add');
        Route::post('/remove', [BookmarkController::class, 'destroy'])->name('bookmark.remove');
    });

    // Job application
    Route::post('/job/apply', [App\Http\Controllers\ApplicationController::class, 'store'])->name('job.apply');

    // Employer permit re-upload (rejected -> pending_review)
    Route::post('/employer/permit/reupload', [App\Http\Controllers\EmployerPermitController::class, 'resubmitPermit'])
        ->name('employer.permit.reupload');

    // Notifications API
    Route::get('/notifications/json', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // AI recommendation endpoints have been removed to keep the system lightweight and focused on verification only.
});

// 🔹 Admin Routes (Protected by admin middleware)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');

    // Unified Verification Management
    Route::get('/verifications/manage', [App\Http\Controllers\Admin\VerificationController::class, 'unified'])->name('admin.verifications.unified');

    // Verification routes
    Route::get('/verifications', [App\Http\Controllers\Admin\VerificationController::class, 'index'])->name('admin.verifications.index');
    Route::get('/verifications/{id}', [App\Http\Controllers\Admin\VerificationController::class, 'show'])->name('admin.verifications.show');
    Route::post('/verifications/{id}/approve', [App\Http\Controllers\Admin\VerificationController::class, 'approve'])->name('admin.verifications.approve');
    Route::post('/verifications/{id}/reject', [App\Http\Controllers\Admin\VerificationController::class, 'reject'])->name('admin.verifications.reject');
    Route::get('/verifications/{id}/file', [App\Http\Controllers\Admin\VerificationController::class, 'file'])->name('admin.verifications.file');

    // Resume verification routes
    Route::post('/resumes/{userId}/approve', [App\Http\Controllers\Admin\VerificationController::class, 'approveResume'])->name('admin.resumes.approve');
    Route::post('/resumes/{userId}/reject', [App\Http\Controllers\Admin\VerificationController::class, 'rejectResume'])->name('admin.resumes.reject');
    Route::get('/resumes/{userId}/view', [App\Http\Controllers\Admin\VerificationController::class, 'viewResume'])->name('admin.resumes.view');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('admin.users.show');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Audit Logs
    Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('admin.audit.index');

    // Admin notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::patch('/notifications/{id}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('admin.notifications.markRead');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('admin.notifications.markAllRead');
    Route::post('/notifications/bulk-mark-read', [App\Http\Controllers\Admin\NotificationController::class, 'bulkMarkRead'])->name('admin.notifications.bulkMarkRead');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::delete('/notifications/bulk-delete', [App\Http\Controllers\Admin\NotificationController::class, 'bulkDelete'])->name('admin.notifications.bulkDelete');
});
