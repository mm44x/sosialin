<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;

use App\Http\Controllers\Admin\ApiLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| Halaman Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services.index');

/*
|--------------------------------------------------------------------------
| Dashboard Bawaan (setelah login & verifikasi)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profil User (Laravel Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users (Admin)
        // Route::get('/users',            [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        // Route::get('/users/export',     [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
        // Route::get('/users/{user}',     [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        // USERS (Admin)
        // Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        // Route::get('/users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export'); // ← sebelum wildcard!
        // Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        // Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        // Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        // Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        // asd
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');

        // Providers
        Route::get('/providers',                 [ProviderController::class, 'index'])->name('providers.index');
        Route::get('/providers/{provider}/edit', [ProviderController::class, 'edit'])->name('providers.edit');
        Route::put('/providers/{provider}',      [ProviderController::class, 'update'])->name('providers.update');
        Route::post('/providers/{provider}/reveal-key', [ProviderController::class, 'revealKey'])
            ->name('providers.reveal-key')
            ->middleware('throttle:reveal-api-key');

        // Services
        Route::resource('services', ServiceController::class)->only(['index', 'edit', 'update']);

        // Categories
        Route::get('/categories',                 [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}',      [CategoryController::class, 'update'])->name('categories.update');

        // API Logs
        Route::get('/api-logs',       [ApiLogController::class, 'index'])->name('api-logs.index');
        Route::get('/api-logs/{log}', [ApiLogController::class, 'show'])->name('api-logs.show');

        // Orders (Admin monitor)
        Route::get('/orders/export', [AdminOrderController::class, 'export'])
            ->name('orders.export');
        Route::post('/orders/bulk-status-check', [AdminOrderController::class, 'bulkStatusCheck'])
            ->name('orders.bulk-status-check')
            ->middleware('throttle:order-status-check');
        Route::get('/orders',         [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/status-check', [AdminOrderController::class, 'statusCheck'])
            ->name('orders.status-check')
            ->middleware('throttle:order-status-check');

        // Transactions (Admin monitor)
        Route::get('/transactions', [TransactionController::class, 'index']) // @phpstan-ignore-line
            ->name('transactions.index');
        Route::get('/transactions/export', [TransactionController::class, 'export']) // @phpstan-ignore-line
            ->name('transactions.export');
    });

/*
|--------------------------------------------------------------------------
| User — Orders
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Form order
    Route::get('/orders/create/{service}', [OrderController::class, 'create'])->name('orders.create');

    // Submit order (opsional: rate-limit "order-submit" bila sudah didefinisikan)
    Route::post('/orders/{service}', [OrderController::class, 'store'])
        ->name('orders.store')
        ->middleware('throttle:order-submit');

    // List & detail
    Route::get('/orders',              [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Cek status manual (opsional: rate-limit khusus)
    Route::post('/orders/{order}/status-check', [OrderController::class, 'statusCheck'])
        ->name('orders.status-check')
        ->middleware('throttle:order-status-check');
});

/*
|--------------------------------------------------------------------------
| User — Wallet
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wallet/topup',        [WalletController::class, 'create'])->name('wallet.topup');

    // Submit topup (opsional: rate-limit "topup-submit" bila sudah didefinisikan)
    Route::post('/wallet/topup',       [WalletController::class, 'store'])
        ->name('wallet.topup.store')
        ->middleware('throttle:topup-submit');

    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
});

require __DIR__ . '/auth.php';
