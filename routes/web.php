<?php

use App\Http\Controllers\Auth\{ForgotPasswordController, LoginController};
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Lead\LeadController;
use App\Http\Controllers\Franchise\FranchiseController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Territory\TerritoryController;
use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('auth.change-password');
    Route::post('/change-password', [LoginController::class, 'changePassword']);

    Route::middleware('must.change.password')->group(function () {

        Route::get('/', function() {
            $user = auth()->user();
            $stats = [
                'active_leads'      => \App\Models\Lead::query()->visibleTo($user)->whereNotIn('stage',['won','lost'])->count(),
                'open_quotes'       => \App\Models\Quotation::query()->visibleTo($user)->whereIn('status',['draft','pending_bdm','pending_zm','pending_sd'])->count(),
                'pending_approvals' => \App\Models\Quotation::query()->visibleTo($user)
                    ->when($user->isBDM(), fn($q)=>$q->where('status','pending_bdm'))
                    ->when($user->isZoneManager(), fn($q)=>$q->whereIn('status',['pending_bdm','pending_zm']))
                    ->when($user->isSalesDirector()||$user->isSuperAdmin(), fn($q)=>$q->whereIn('status',['pending_bdm','pending_zm','pending_sd']))
                    ->count(),
                'won_this_month'    => \App\Models\Lead::query()->visibleTo($user)->where('stage','won')->whereMonth('updated_at',date('m'))->count(),
                'pipeline_value'    => \App\Models\Quotation::query()->visibleTo($user)->whereIn('status',['pending_bdm','pending_zm','pending_sd','approved'])->sum('total'),
                'won_value'         => \App\Models\Quotation::query()->visibleTo($user)->where('status','won')->whereMonth('updated_at',date('m'))->sum('total'),
            ];
            $recentLeads  = \App\Models\Lead::with(['customer','assignedTo'])->visibleTo($user)->latest()->limit(5)->get();
            $recentQuotes = \App\Models\Quotation::with(['customer','product'])->visibleTo($user)->latest()->limit(5)->get();
            $overdueFollowups = \App\Models\Lead::query()->visibleTo($user)->where('follow_up_at','<',now())->whereNotIn('stage',['won','lost'])->count();
            return view('dashboard', compact('stats','recentLeads','recentQuotes','overdueFollowups'));
        })->name('dashboard');

        Route::get('/profile', fn() => view('coming-soon',['module'=>'My Profile']))->name('profile.edit');

        Route::resource('leads', LeadController::class);
        Route::resource('franchises', FranchiseController::class);
        Route::resource('quotations', QuotationController::class);

        Route::post('/quotations/{quotation}/submit', [QuotationController::class, 'submit'])->name('quotations.submit');
        Route::post('/quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
        Route::post('/quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
        Route::post('/quotations/{quotation}/revision', [QuotationController::class, 'requestRevision'])->name('quotations.revision');
        Route::post('/quotations/{quotation}/won', [QuotationController::class, 'markWon'])->name('quotations.won');
        Route::post('/quotations/{quotation}/lost', [QuotationController::class, 'markLost'])->name('quotations.lost');

        Route::get('/approvals', [QuotationController::class, 'pendingApprovals'])->name('approvals.index');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/quotations', [ReportController::class, 'quotations'])->name('reports.quotations');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', UserController::class);
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::get('/users/districts', [UserController::class, 'getDistricts'])->name('users.districts');
            Route::get('/users/cities', [UserController::class, 'getCities'])->name('users.cities');
            Route::get('/territories', [TerritoryController::class, 'index'])->name('territories.index');
            Route::post('/territories', [TerritoryController::class, 'store'])->name('admin.territories.store');
            Route::put('/territories/{territory}', [TerritoryController::class, 'update'])->name('admin.territories.update');
            Route::patch('/territories/{territory}/toggle', [TerritoryController::class, 'toggle'])->name('admin.territories.toggle');
            Route::delete('/territories/{territory}', [TerritoryController::class, 'destroy'])->name('admin.territories.destroy');
            Route::get('/territories/districts', [TerritoryController::class, 'getDistricts'])->name('admin.territories.districts');
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::post('/products/addons', [ProductController::class, 'storeAddon'])->name('products.addons.store');
            Route::get('/products/addons/list', [ProductController::class, 'getAddons'])->name('products.addons.list');
        });
    });
});
