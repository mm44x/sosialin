<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <!-- Animated Background -->
    <div class="fixed inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
        <!-- Floating Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl pulse-glow floating"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-indigo-400/20 to-pink-400/20 rounded-full blur-3xl pulse-glow floating floating-delay-1"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-purple-400/10 to-blue-400/10 rounded-full blur-3xl pulse-glow floating floating-delay-2"></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo Section -->
        <div class="relative z-10 mb-8 slide-in-up">
            <a href="/" class="group">
                <div class="w-24 h-24 bg-gradient-to-br from-primary to-purple-600 rounded-2xl shadow-lg flex items-center justify-center group-hover:scale-105 transition-transform duration-300 hover-lift">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="mt-4 text-center">
                    <h1 class="text-2xl font-bold gradient-text">
                        {{ config('app.name', 'Sosialin') }}
                    </h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Platform SMM Terpercaya</p>
                </div>
            </a>
        </div>

        <!-- Auth Card -->
        <div class="relative z-10 w-full sm:max-w-md px-6 py-8 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-2xl rounded-3xl border border-white/20 dark:border-slate-700/20 slide-in-up-delay-1 card-hover">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="relative z-10 mt-8 text-center slide-in-up-delay-2">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                © {{ date('Y') }} {{ config('app.name', 'Sosialin') }}. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Particles Animation -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-blue-400/30 rounded-full floating"></div>
        <div class="absolute top-1/3 right-1/4 w-1 h-1 bg-purple-400/40 rounded-full floating floating-delay-1"></div>
        <div class="absolute bottom-1/4 left-1/3 w-1.5 h-1.5 bg-indigo-400/35 rounded-full floating floating-delay-2"></div>
        <div class="absolute bottom-1/3 right-1/3 w-1 h-1 bg-pink-400/40 rounded-full floating floating-delay-3"></div>
    </div>
</body>

</html>
