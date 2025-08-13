<nav class="border-b border-slate-200 dark:border-slate-700 bg-bg text-text dark:bg-bgDark dark:text-white">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 font-semibold">
            {{-- Wordmark SVG / Placeholder --}}
            @include('partials.brand')
        </a>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('dashboard') }}"
                    class="px-3 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary">
                    Dashboard
                </a>

                <a href="{{ route('orders.index') }}"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10
              focus:outline-none focus:ring-2 focus:ring-primary
              {{ request()->routeIs('orders.*') ? 'bg-primary/20' : '' }}">
                    Riwayat Order
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary">
                    Masuk
                </a>
                <a href="{{ route('register') }}"
                    class="px-3 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary">
                    Daftar
                </a>
            @endauth

            @auth
                @if (auth()->user()->role === 'admin')
                    <details class="relative">
                        <summary
                            class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 cursor-pointer
               hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary list-none">
                            Admin
                        </summary>
                        <div
                            class="absolute right-0 mt-2 w-56 z-50 rounded-xl overflow-hidden
                  bg-white dark:bg-slate-800 shadow-lg ring-1 ring-slate-200/60 dark:ring-white/10">
                            <a href="{{ route('admin.dashboard') }}"
                                class="block px-4 py-2 text-sm hover:bg-primary/10
                  {{ request()->routeIs('admin.dashboard') ? 'bg-primary/20' : '' }}">Dashboard</a>
                            <a href="{{ route('admin.providers.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-primary/10
                  {{ request()->routeIs('admin.providers.*') ? 'bg-primary/20' : '' }}">Providers</a>
                            <a href="{{ route('admin.services.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-primary/10
                  {{ request()->routeIs('admin.services.*') ? 'bg-primary/20' : '' }}">Services</a>
                            <a href="{{ route('admin.categories.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-primary/10
                  {{ request()->routeIs('admin.categories.*') ? 'bg-primary/20' : '' }}">Categories</a>
                            <a href="{{ route('admin.api_logs.index') }}"
                                class="block px-4 py-2 text-sm hover:bg-primary/10
          {{ request()->routeIs('admin.api_logs.*') ? 'bg-primary/20' : '' }}">API
                                Logs</a>

                        </div>
                    </details>
                @endif
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
                                <div> {{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        aria-hidden="true">
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


            {{-- Theme Toggle --}}
            <button id="themeToggle" type="button"
                class="ml-1 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary"
                aria-label="Toggle dark mode">
                🌗
            </button>
        </div>
    </div>
</nav>
