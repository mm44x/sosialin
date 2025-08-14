<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Rate limiter untuk endpoint sensitif
            RateLimiter::for('order-submit', function (Request $request) {
                // Maks 10 order per menit per user (fallback ke IP jika guest)
                return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
            });

            RateLimiter::for('topup-submit', function (Request $request) {
                // Maks 5 topup/menit dan 50/hari per user (fallback ke IP)
                return [
                    Limit::perMinute(5)->by($request->user()?->id ?? $request->ip()),
                    Limit::perDay(50)->by($request->user()?->id ?? $request->ip()),
                ];
            });

            RateLimiter::for('order-status-check', function (Request $request) {
                // Maks 10 cek status/menit per user
                return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
            });

            RateLimiter::for('reveal-api-key', function (Request $request) {
                // Maks 10 kali/menit per admin/user
                return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware kustom
        $middleware->alias([
            'admin'  => \App\Http\Middleware\EnsureAdmin::class,
            'active' => \App\Http\Middleware\EnsureUserActive::class, // bisa dipakai per-route bila perlu
        ]);

        // Pastikan user non-aktif (banned) otomatis logout di SEMUA web routes
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EnsureUserActive::class,
        ]);

        // (biarkan middleware default Laravel lainnya)
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('smm:poll-orders')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    })
    ->withProviders([
        App\Providers\WalletServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
