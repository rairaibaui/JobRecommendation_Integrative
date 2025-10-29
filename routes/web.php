<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
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

// 🔹 Routes that require authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Recommendation
    Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation');

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
});
