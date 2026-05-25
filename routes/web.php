<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ───────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// ── Authenticated routes ───────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Force password change
    Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('auth.change-password');
    Route::post('/change-password', [LoginController::class, 'changePassword']);

    // All routes below also require password change check
    Route::middleware(['must.change.password'])->group(function () {

        // Dashboard
        Route::get('/', fn() => view('dashboard'))->name('dashboard');

        // Leads (placeholder - Module 2)
        Route::get('/leads', fn() => view('coming-soon', ['module' => 'Leads & Customers']))->name('leads.index');

        // Quotations (placeholder - Module 3)
        Route::get('/quotations', fn() => view('coming-soon', ['module' => 'Quotations']))->name('quotations.index');

        // Approvals (placeholder - Module 4)
        Route::get('/approvals', fn() => view('coming-soon', ['module' => 'Approvals']))->name('approvals.index');

        // Franchises (placeholder - Module 5)
        Route::get('/franchises', fn() => view('coming-soon', ['module' => 'Franchise Management']))->name('franchises.index');

        // Reports (placeholder - Module 6)
        Route::get('/reports', fn() => view('coming-soon', ['module' => 'Reports']))->name('reports.index');

        // Profile
        Route::get('/profile', fn() => view('coming-soon', ['module' => 'My Profile']))->name('profile.edit');

        // ── Admin routes ─────────────────────────────────────────
        Route::prefix('admin')->name('admin.')->group(function () {

            // User Management
            Route::resource('users', UserController::class);
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::get('/users/districts', [UserController::class, 'getDistricts'])->name('users.districts');
            Route::get('/users/cities', [UserController::class, 'getCities'])->name('users.cities');

            // Territories (placeholder - Module 2)
            Route::get('/territories', fn() => view('coming-soon', ['module' => 'Territory Management']))->name('territories.index');

            // Products & Pricing (placeholder - Module 3)
            Route::get('/products', fn() => view('coming-soon', ['module' => 'Products & Pricing']))->name('products.index');

            // Audit Logs (placeholder)
            Route::get('/audit-logs', fn() => view('coming-soon', ['module' => 'Audit Logs']))->name('audit-logs.index');
        });
    });
});
