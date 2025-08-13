<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($code ?? 'Error') }} — {{ config('app.name', 'Sosialin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-bg text-text dark:bg-bgDark dark:text-white antialiased">
    <div class="min-h-full flex items-center justify-center px-4">
        <div class="max-w-xl w-full text-center">
            <div
                class="mb-6 inline-flex items-center justify-center h-16 w-16 rounded-2xl
                        ring-1 ring-slate-200/60 dark:ring-white/10 bg-white/70 dark:bg-white/5">
                <span class="text-2xl font-bold">{{ $code ?? '!!' }}</span>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold">
                {{ $title ?? ($message ?? 'Terjadi kesalahan') }}
            </h1>

            @isset($subtitle)
                <p class="mt-2 text-slate-600 dark:text-slate-300">{{ $subtitle }}</p>
            @else
                <p class="mt-2 text-slate-600 dark:text-slate-300">
                    @switch($code ?? null)
                        @case(404)
                            Halaman tidak ditemukan.
                        @break

                        @case(419)
                            Sesi kedaluwarsa atau token CSRF tidak valid.
                        @break

                        @case(429)
                            Terlalu banyak permintaan. Coba lagi sebentar.
                        @break

                        @case(500)
                            Terjadi kesalahan pada server.
                        @break

                        @default
                            Silakan coba lagi atau hubungi admin.
                    @endswitch
                </p>
            @endisset

            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <button type="button" onclick="history.back()"
                    class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                    Kembali
                </button>

                <a href="{{ route('home') }}"
                    class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                    Ke Beranda
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        Dashboard
                    </a>
                @endauth
            </div>

            @if (config('app.debug'))
                <details
                    class="mt-6 text-left text-xs open:shadow-soft open:bg-white/70 dark:open:bg-white/5
                                rounded-2xl p-4 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <summary class="cursor-pointer">Detail debug</summary>
                    <pre class="mt-2 whitespace-pre-wrap">{{ $message ?? '' }}</pre>
                </details>
            @endif
        </div>
    </div>
</body>

</html>
