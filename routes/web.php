<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    
    $featuredProperties = \App\Models\Property::with(['primaryImage', 'owner'])
        ->available()
        ->latest()
        ->take(8)
        ->get();
        
    return view('welcome', compact('featuredProperties'));
})->name('home');

// Public Host Profile Route
Route::get('/host/{user}', [\App\Http\Controllers\HostController::class, 'show'])->name('host.show');

// Public Property Details Route
Route::get('/properties/{property}', [\App\Http\Controllers\PropertyController::class, 'show'])->name('properties.show');

// --- Role-Based Access Control (RBAC) Routes --- //

// 1. Admin Only Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // KYC
    Route::patch('/kyc/{document}/approve', [\App\Http\Controllers\ComplianceController::class, 'approve'])->name('kyc.approve');
    Route::patch('/kyc/{document}/reject', [\App\Http\Controllers\ComplianceController::class, 'reject'])->name('kyc.reject');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.updateRole');
    Route::patch('/users/{user}/ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('users.toggleBan');

    // Properties
    Route::get('/properties', [\App\Http\Controllers\Admin\PropertyController::class, 'index'])->name('properties.index');
    Route::patch('/properties/{property}/ban', [\App\Http\Controllers\Admin\PropertyController::class, 'toggleBan'])->name('properties.toggleBan');
});

// 2. Landlord Only Routes
Route::middleware(['auth', 'role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
    // Compliance & Business Requirements
    Route::get('/compliance', [\App\Http\Controllers\ComplianceController::class, 'index'])->name('compliance.index');
    Route::post('/compliance', [\App\Http\Controllers\ComplianceController::class, 'store'])->name('compliance.store');

    // Maintenance Requests
    Route::get('/maintenance', [\App\Http\Controllers\MaintenanceRequestController::class, 'index'])->name('maintenance.index');
    Route::patch('/maintenance/{maintenance}', [\App\Http\Controllers\MaintenanceRequestController::class, 'update'])->name('maintenance.update');
});

// 3. Tenant Only Routes
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/applications', [\App\Http\Controllers\ApplicationController::class, 'tenantIndex'])->name('applications.index');
    
    // Maintenance Requests
    Route::get('/maintenance', [\App\Http\Controllers\MaintenanceRequestController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [\App\Http\Controllers\MaintenanceRequestController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [\App\Http\Controllers\MaintenanceRequestController::class, 'store'])->name('maintenance.store');
    
    // Leases
    Route::get('/leases/{lease}/sign', [\App\Http\Controllers\LeaseController::class, 'sign'])->name('leases.sign');
    Route::post('/leases/{lease}/sign', [\App\Http\Controllers\LeaseController::class, 'processSignature'])->name('leases.processSignature');
    
    // Invoices / Payments
    Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices/{invoice}/pay', [\App\Http\Controllers\InvoiceController::class, 'pay'])->name('invoices.pay');
    
    // Favorites
    Route::get('/favorites', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
});

// Shared routes for both Tenant and Landlord to view properties
Route::middleware(['auth'])->group(function () {
    // Wallet
    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/withdraw', [\App\Http\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');

    Route::get('/properties', [\App\Http\Controllers\PropertyController::class, 'index'])->name('properties.index');
});

// Help Center (Public or Authenticated)
Route::view('/help', 'help.index')->name('help.index');

// 4. Shared Routes (Landlord & Tenant & Admin)
// These routes can be accessed by multiple roles if needed
Route::middleware(['auth', 'role:admin,landlord,tenant'])->group(function () {
    // e.g. Route::get('/messages', [MessageController::class, 'index'])->name('messages');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/properties/{property}/applications', [\App\Http\Controllers\ApplicationController::class, 'store'])->name('applications.store');
    
    // Reviews
    Route::post('/properties/{property}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('properties.reviews.store');
    Route::post('/tenants/{tenant}/reviews', [App\Http\Controllers\ReviewController::class, 'storeTenantReview'])->name('tenants.reviews.store');
    
    Route::post('/properties/{property}/favorite', [\App\Http\Controllers\PropertyController::class, 'toggleFavorite'])->name('properties.favorite');
    Route::patch('/applications/{application}/status', [\App\Http\Controllers\ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('properties', PropertyController::class)->except(['show']);
    
    Route::get('/leases/{lease}/download', [\App\Http\Controllers\LeaseController::class, 'downloadContract'])->name('leases.download');
    
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::patch('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Profile routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Google Auth Routes
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('google.callback');

// Onboarding Route
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');
});

require __DIR__.'/auth.php';
