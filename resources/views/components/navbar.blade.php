<nav
    class="sticky top-0 z-50 backdrop-blur-lg bg-white/80 dark:bg-slate-900/80 border-b border-slate-200/80 dark:border-slate-800/80">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between gap-8">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    {{-- Logo with gradient --}}
                    <div
                        class="w-8 h-8 rounded bg-gradient-to-tr from-primary to-purple-600 flex items-center justify-center text-white font-bold">
                        S
                    </div>
                    {{-- Brand name with gradient text --}}
                    <span
                        class="text-xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                        Sosialin
                    </span>
                </a>

                {{-- Desktop Navigation Links --}}
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('services.index') }}"
                        class="text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors">
                        Services
                    </a>
                    <a href="#features"
                        class="text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors">
                        Features
                    </a>
                    <a href="#testimonials"
                        class="text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors">
                        Testimonials
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    {{-- Authenticated Navigation --}}
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-primary to-purple-600 
                           text-white font-medium shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('orders.index') }}"
                        class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                           hover:border-primary/20 dark:hover:border-primary/20 hover:bg-primary/5 dark:hover:bg-primary/5
                           transition-all duration-300 {{ request()->routeIs('orders.*') ? 'bg-primary/10' : '' }}">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-primary transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Riwayat Order</span>
                    </a>
                @else
                    {{-- Guest Navigation --}}
                    <a href="{{ route('login') }}"
                        class="group px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 font-medium
                           hover:border-primary/20 dark:hover:border-primary/20 hover:bg-primary/5 dark:hover:bg-primary/5
                           transition-all duration-300">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-primary to-purple-600 text-white font-medium
                           shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
                        Daftar Sekarang
                    </a>
                @endauth

                @auth
                    @if (auth()->user()->role === 'admin')
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open"
                                class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                                   hover:border-primary/20 dark:hover:border-primary/20 hover:bg-primary/5 dark:hover:bg-primary/5
                                   transition-all duration-300">
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-primary transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Admin</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 rounded-xl overflow-hidden bg-white dark:bg-slate-800 
                                    shadow-lg ring-1 ring-slate-200/60 dark:ring-slate-700/60 py-1">

                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    <span>Dashboard</span>
                                </a>

                                <a href="{{ route('admin.users.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>Users</span>
                                </a>

                                <a href="{{ route('admin.orders.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span>Orders</span>
                                </a>

                                <a href="{{ route('admin.transactions.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.transactions.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Transactions</span>
                                </a>

                                <div class="border-t border-slate-200 dark:border-slate-700 my-1"></div>

                                <a href="{{ route('admin.services.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.services.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>Services</span>
                                </a>

                                <a href="{{ route('admin.categories.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.categories.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span>Categories</span>
                                </a>

                                <a href="{{ route('admin.api-logs.index') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-primary/5 transition-colors
                                       {{ request()->routeIs('admin.api-logs.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>API Logs</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Saldo --}}
                    @php $saldo = optional(auth()->user()->wallet)->balance ?? 0; @endphp
                    <a href="{{ route('wallet.topup') }}"
                        class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                           hover:border-primary/20 dark:hover:border-primary/20 hover:bg-primary/5 dark:hover:bg-primary/5
                           transition-all duration-300">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                    </a>
                @endauth

                {{-- Settings Dropdown (only when authenticated) --}}
                @auth
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md
                                border border-slate-300 bg-white text-gray-700 hover:text-gray-900
                                dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:text-white
                                focus:outline-none transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="currentColor" aria-hidden="true">
                                        <path
                                            d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-5.33 0-8 2.67-8 5v1h16v-1c0-2.33-2.67-5-8-5Z" />
                                    </svg>
                                    <div class="ml-2">{{ Auth::user()->name }}</div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth

                {{-- Settings Dropdown — MOBILE (sm:hidden) --}}
                @auth
                    <div class="flex sm:hidden">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center p-2 rounded-lg
                                border border-slate-300 bg-white text-gray-700
                                hover:text-gray-900
                                dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:text-white
                                focus:outline-none transition">
                                    <span class="sr-only">Open user menu</span>
                                    <!-- User icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="currentColor" aria-hidden="true">
                                        <path
                                            d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-5.33 0-8 2.67-8 5v1h16v-1c0-2.33-2.67-5-8-5Z" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth

                {{-- Theme Toggle (Sun/Moon) --}}
                <button id="themeToggle" type="button"
                    class="ml-1 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary"
                    aria-label="Toggle theme" title="Toggle theme">
                    <span class="inline-flex items-center gap-2">
                        <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="4" />
                            <path
                                d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.536-7.536-1.414 1.414M7.05 16.95l-1.414 1.414M19.364 19.364l-1.414-1.414M7.05 7.05 5.636 5.636" />
                        </svg>
                        <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                        {{-- <span class="text-sm">Theme</span> --}}
                    </span>
                </button>
            </div>
        </div>
</nav>
