<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sosialin') }}</title>
    <!-- Anti-FOUC: set tema sedini mungkin -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('theme');
                const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const useDark = saved ? (saved === 'dark') : systemDark;
                document.documentElement.classList.toggle('dark', !!useDark);
            } catch (e) {}
        })();
    </script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-bg text-text dark:bg-bgDark dark:text-white antialiased">
    <x-navbar />
    <x-toast />

    <main id="content" class="min-h-[70vh]">
        @yield('content')
    </main>

    <footer class="border-t bg-bg/60 dark:bg-bgDark/60">
        <div class="container mx-auto px-4 py-8 text-sm text-slateText dark:text-slate-300">
            <div class="flex flex-col md:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} Sosialin. All rights reserved.</p>
                <nav class="flex gap-4">
                    <a class="hover:underline" href="{{ route('services.index') }}">Services</a>
                    <a class="hover:underline" href="{{ route('login') }}">Login</a>
                    <a class="hover:underline" href="{{ route('register') }}">Register</a>
                </nav>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
