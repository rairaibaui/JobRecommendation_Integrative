<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\EmployerApplicantsController;
use App\Http\Controllers\EmployerHistoryController;
use App\Http\Controllers\EmployerEmployeesController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MyApplicationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
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

// Redirect root to login
Route::get('/', fn () => redirect()->route('login'));

// 🔹 Authentication Routes

// Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// 🔹 Routes that require authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Employer Dashboard
    Route::get('/employer/dashboard', [EmployerDashboardController::class, 'index'])->name('employer.dashboard');
    // Employer Applicants Management
    Route::get('/employer/applicants', [EmployerApplicantsController::class, 'index'])->name('employer.applicants');
    Route::post('/employer/applications/{application}/status', [EmployerApplicantsController::class, 'updateStatus'])->name('employer.applications.updateStatus');
    Route::delete('/employer/applications/{application}', [EmployerApplicantsController::class, 'destroy'])->name('employer.applications.destroy');
    
    // Employer History (Hired/Rejected Records)
    Route::get('/employer/history', [EmployerHistoryController::class, 'index'])->name('employer.history');
    // Employer Employees (Accepted/Hired applicants)
    Route::get('/employer/employees', [EmployerEmployeesController::class, 'index'])->name('employer.employees');
    
    // Employer Job Postings
    Route::get('/employer/jobs', [JobPostingController::class, 'index'])->name('employer.jobs');
    Route::get('/employer/jobs/create', [JobPostingController::class, 'create'])->name('employer.jobs.create');
    Route::post('/employer/jobs', [JobPostingController::class, 'store'])->name('employer.jobs.store');
    Route::delete('/employer/jobs/{jobPosting}', [JobPostingController::class, 'destroy'])->name('employer.jobs.destroy');

    // Recommendation
    Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation');

    // My Applications
    Route::get('/my-applications', [MyApplicationsController::class, 'index'])->name('my-applications');
    Route::delete('/my-applications/{application}', [MyApplicationsController::class, 'destroy'])->name('my-applications.destroy');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');

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
        Route::get('/resume', [ProfileController::class, 'resume'])->name('profile.resume');
        Route::post('/change-email', [ProfileController::class, 'changeEmail'])->name('profile.changeEmail');
        Route::post('/change-phone', [ProfileController::class, 'changePhone'])->name('profile.changePhone');
        Route::post('/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');

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

    // Notifications API
    Route::get('/notifications/json', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});
