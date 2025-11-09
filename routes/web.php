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

// Email verification routes (simple helpers)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\URL as URLFacade;

// Verification link: allow the signed URL to verify even when the user is not currently logged in.
Route::get('/email/verify/{id}/{hash}', function (HttpRequest $request, $id, $hash) {
    // Validate that the URL signature is valid (protects against tampering and enforces expiry)
    if (! URLFacade::hasValidSignature($request)) {
        // If the link is expired or invalid, handle based on whether the visitor
        // is currently authenticated. For unauthenticated requests we redirect
        // back to login with a generic error. If the recipient happens to be
        // authenticated (rare), show the friendly expired page that allows a
        // public resend flow without leaking account existence.
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        $user = \App\Models\User::find($id);
        if ($user && ! $user->hasVerifiedEmail()) {
            return response()->view('auth.verify-expired', ['user' => $user]);
        }

        return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
    }

    // At this point the signed URL is valid and within its configured expiry window.
    // Find the user and verify the email hash matches
    $user = \App\Models\User::find($id);
    if (! $user) {
        return redirect()->route('login')->with('error', 'Invalid verification link (user not found).');
    }

    // The hash in the link should match the SHA1 of the user's email as per Laravel's default
    if (sha1($user->getEmailForVerification()) !== (string) $hash) {
        return redirect()->route('login')->with('error', 'Verification link does not match user.');
    }

    // Mark as verified if not already
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // If the request came from an authenticated session that matches the
    // verified user, keep them in the app and send them to the dashboard.
    // Otherwise redirect to the login page so they can sign in.
    if ($request->user() && intval($request->user()->id) === intval($id)) {
        return redirect()->route('dashboard')->with('success', 'Your email has been verified.');
    }

    return redirect()->route('login')->with('success', 'Your email has been verified. You may now sign in.');

})->name('verification.verify');

Route::post('/email/resend', function (\Illuminate\Http\Request $request) {
    try {
        if (!$request->user()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'You must be signed in to request a verification email.'], 401);
            }

            return redirect()->route('login')->with('error', 'You must be signed in to request a verification email.');
        }

        $request->user()->sendEmailVerificationNotification();

        // If this was called via AJAX/fetch expecting JSON, return JSON; otherwise preserve original redirect/back behavior
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Verification link sent to your email address.']);
        }

        return back()->with('success', 'Verification link sent to your email address.');
    } catch (\Throwable $e) {
        // Log the failure and show a friendly message without exposing internals
        \Illuminate\Support\Facades\Log::error('Failed to resend verification email', ['user_id' => optional($request->user())->id, 'error' => $e->getMessage()]);
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Unable to send verification email right now. Please try again later or contact support.'], 500);
        }

        return back()->with('error', 'Unable to send verification email right now. Please try again later or contact support.');
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// Public resend endpoint for expired verification links (rate limited to prevent abuse)
Route::post('/email/resend-public', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'user_id' => 'required|integer',
    ]);

    try {
        $user = \App\Models\User::find($request->input('user_id'));
        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Failed to resend verification email (public)', ['user_id' => $request->input('user_id'), 'error' => $e->getMessage()]);
    }

    // Don't reveal whether the account exists; return back to the expired link page
    // with a generic message so the visitor stays on the same page instead of being
    // sent to the login screen (avoids signing them out or causing confusion).
    return redirect()->back()->with('success', 'If an account exists for that link, a new verification email has been sent.');
})->middleware('throttle:3,1')->name('verification.resend.public');

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
    // Start email change flow: sends OTP to new email
    Route::post('/change-email', [ProfileController::class, 'sendEmailOTP'])->middleware('throttle:6,1')->name('profile.changeEmail');
    // Verify the OTP and complete email change
    Route::post('/verify-email-otp', [ProfileController::class, 'verifyEmailOTP'])->middleware('throttle:6,1')->name('profile.verifyEmailOTP');
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
    // Details page with AI extraction and mismatch details
    Route::get('/resumes/{userId}/details', [App\Http\Controllers\Admin\VerificationController::class, 'resumeDetails'])->name('admin.resumes.details');

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
