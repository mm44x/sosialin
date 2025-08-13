<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ApiLogController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Admin\DashboardController;

// Halaman publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services.index');

// Dashboard default (boleh tetap pakai view bawaan)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil user (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Providers
        Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
        Route::get('/providers/{provider}/edit', [ProviderController::class, 'edit'])->name('providers.edit');
        Route::put('/providers/{provider}', [ProviderController::class, 'update'])->name('providers.update');
        Route::post('/providers/{provider}/reveal-key', [ProviderController::class, 'revealKey'])
            ->name('providers.reveal-key')
            ->middleware('throttle:reveal-api-key');

        // Services
        Route::resource('services', ServiceController::class)->only(['index', 'edit', 'update']);

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

        // API Logs
        Route::get('/api-logs', [ApiLogController::class, 'index'])->name('api-logs.index');
        Route::get('/api-logs/{log}', [ApiLogController::class, 'show'])->name('api-logs.show');
    });

// USER — Orders
Route::middleware(['auth', 'verified'])->group(function () {
    // Form order (GET)
    Route::get('/orders/create/{service}', [OrderController::class, 'create'])->name('orders.create');
    // Submit order (POST) + throttle limiter
    Route::post('/orders/{service}', [OrderController::class, 'store'])
        ->name('orders.store')
        ->middleware('throttle:order-submit');

    // List & detail order
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Refresh status manual + throttle
    Route::post('/orders/{order}/status-check', [OrderController::class, 'statusCheck'])
        ->name('orders.status-check')
        ->middleware('throttle:order-status-check');
});

// USER — Wallet
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wallet/topup', [WalletController::class, 'create'])->name('wallet.topup');
    Route::post('/wallet/topup', [WalletController::class, 'store'])
        ->name('wallet.topup.store')
        ->middleware('throttle:topup-submit');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
});

require __DIR__ . '/auth.php';
