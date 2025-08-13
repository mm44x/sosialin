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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services.index');

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
        Route::get('/providers/{provider}/edit', [ProviderController::class, 'edit'])->name('providers.edit');
        Route::put('/providers/{provider}', [ProviderController::class, 'update'])->name('providers.update');
        Route::post('/providers/{provider}/reveal-key', [ProviderController::class, 'revealKey'])
            ->name('providers.reveal-key')
            ->middleware('throttle:reveal-api-key');
        Route::resource('services', ServiceController::class)->only(['index', 'edit', 'update']);
        Route::resource('categories', CategoryController::class)->only(['index', 'edit', 'update']);
        Route::get('/api-logs', [ApiLogController::class, 'index'])->name('api-logs.index');
        Route::get('/api-logs/{api_log}', [ApiLogController::class, 'show'])->name('api-logs.show');
    });


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders/create/{service}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/{service}',        [OrderController::class, 'store'])->name('orders.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wallet/topup', [WalletController::class, 'create'])->name('wallet.topup');
    Route::post('/wallet/topup', [WalletController::class, 'store'])->name('wallet.topup.store');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders',                [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}',   [OrderController::class, 'show'])->name('orders.show');
    // Route::post('/orders/{order}/refresh-status', [OrderController::class, 'refreshStatus'])->name('orders.refresh');
    Route::post('/orders/{order}/status-check', [\App\Http\Controllers\OrderController::class, 'statusCheck'])
        ->name('orders.status-check')
        ->middleware('throttle:order-status-check');
});



require __DIR__ . '/auth.php';
