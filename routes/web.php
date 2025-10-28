<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// 🔹 Register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// 🔹 Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// 🔹 Dashboard (requires authentication)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// 🔹 Recommendation (requires authentication)
Route::get('/recommendation', [DashboardController::class, 'recommendation'])->name('recommendation')->middleware('auth');

// 🔹 Bookmarks (requires authentication)
Route::get('/bookmarks', [DashboardController::class, 'bookmarks'])->name('bookmarks')->middleware('auth');

// 🔹 Settings (requires authentication)
Route::get('/settings', [DashboardController::class, 'settings'])->name('settings')->middleware('auth');

// 🔹 Clear Bookmarks (requires authentication)
Route::post('/clear-bookmarks', [DashboardController::class, 'clearBookmarks'])->name('clear.bookmarks')->middleware('auth');

// 🔹 Change Password (requires authentication)
Route::get('/change-password', [DashboardController::class, 'changePassword'])->name('change.password')->middleware('auth');
Route::post('/change-password', [DashboardController::class, 'updatePassword'])->name('change.password.submit')->middleware('auth');

// 🔹 Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-email', [ProfileController::class, 'changeEmail'])->name('profile.changeEmail');
    Route::post('/profile/change-phone', [ProfileController::class, 'changePhone'])->name('profile.changePhone');
    Route::middleware(['auth'])->group(function () {
        Route::post('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
        Route::post('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate')->middleware('auth');
    });
});
