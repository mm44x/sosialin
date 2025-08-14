<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Hanya cek jika ada user yang terautentikasi
        if ($user && (int)($user->is_active ?? 1) === 0) {
            // Putuskan sesi
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // AJAX/JSON -> 403 JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda diblokir (banned). Silakan hubungi admin.',
                ], 403);
            }

            // Request biasa -> redirect ke login + error
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda diblokir (banned). Silakan hubungi admin.']);
        }

        return $next($request);
    }
}
