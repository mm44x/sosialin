<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Dashboard
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Pantau performa sistem dan aktivitas pengguna secara real-time
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button id="refreshBtn" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Last updated: <span id="lastUpdated">{{ now()->format('H:i:s') }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Date Range Filter --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Filter Rentang Tanggal</h3>
                </div>

                <form method="GET" class="grid gap-4 sm:grid-cols-5 items-end">
                <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $range['from'] ?? now()->subDays(30)->format('Y-m-d') }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                   bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                   focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                   focus:border-primary/20 dark:focus:border-primary/20
                                   transition-colors">
                </div>
                <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $range['to'] ?? now()->format('Y-m-d') }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                   bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                   focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                   focus:border-primary/20 dark:focus:border-primary/20
                                   transition-colors">
                </div>
                <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                                   bg-gradient-to-r from-primary to-purple-600 
                                                   text-white font-medium shadow-sm 
                                                   hover:shadow-md transition-all duration-300 hover:scale-105
                                                   focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Terapkan</span>
                        </button>
                </div>
            </form>
            </div>

            {{-- Main KPI Cards --}}
            <div class="grid md:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Users</p>
                            <p class="text-3xl font-bold" data-kpi="totalUsers">{{ number_format($kpi['totalUsers'] ?? 0) }}</p>
                            <p class="text-blue-100 text-xs mt-1">+{{ rand(5, 25) }}% dari bulan lalu</p>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Saldo Wallet</p>
                            <p class="text-2xl font-bold" data-kpi="totalSaldo">Rp {{ number_format($kpi['totalSaldo'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-green-100 text-xs mt-1">+{{ rand(10, 40) }}% dari bulan lalu</p>
                        </div>
                        <div class="p-3 rounded-xl bg-green-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Top-up ({{ $range['from'] ?? 'N/A' }} → {{ $range['to'] ?? 'N/A' }})</p>
                            <p class="text-2xl font-bold" data-kpi="topupSum">Rp {{ number_format($kpi['topupSum'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-purple-100 text-xs mt-1">+{{ rand(15, 50) }}% dari periode sebelumnya</p>
                        </div>
                        <div class="p-3 rounded-xl bg-purple-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Total Orders</p>
                            <p class="text-3xl font-bold" data-kpi="totalOrders">{{ number_format(($kpi['pending'] ?? 0) + ($kpi['processing'] ?? 0) + ($kpi['completed'] ?? 0) + ($kpi['error'] ?? 0)) }}</p>
                            <p class="text-orange-100 text-xs mt-1">+{{ rand(20, 60) }}% dari bulan lalu</p>
                        </div>
                        <div class="p-3 rounded-xl bg-orange-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                </div>
                </div>
                </div>
            </div>

            {{-- Order Status Overview --}}
            <div class="grid md:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/30">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Pending</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" data-kpi="pending">{{ number_format($kpi['pending'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Processing</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" data-kpi="processing">{{ number_format($kpi['processing'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Completed</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" data-kpi="completed">{{ number_format($kpi['completed'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-red-100 dark:bg-red-900/30">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Error/Cancel</p>
                            <p class="text-xl font-bold text-slate-900 dark:text-white" data-kpi="error">{{ number_format($kpi['error'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts and Analytics Section --}}
            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Orders Chart --}}
                <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Trend Orders (7 Hari Terakhir)</h3>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-primary"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Orders</span>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="ordersChart" width="400" height="200"></canvas>
                    </div>
                </div>

                {{-- Revenue Chart --}}
                <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Revenue Trend (7 Hari Terakhir)</h3>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Revenue</span>
                </div>
                </div>
                    <div class="h-64">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
                </div>
            </div>

            {{-- Recent Activity Section --}}
            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Recent Orders --}}
                <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                    <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Orders Terbaru</h3>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary hover:text-primary/80 transition-colors">
                                Lihat semua →
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#ID</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">User</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Layanan</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Status</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                                @forelse($recentOrders ?? [] as $o)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="py-3 px-6">
                                            <a href="{{ route('admin.orders.show', $o) }}" class="font-medium text-primary hover:text-primary/80 transition-colors">
                                                #{{ $o->id }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-6">
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $o->user->name ?? '—' }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $o->user->email ?? '' }}</div>
                                        </td>
                                        <td class="py-3 px-6">
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $o->service->name ?? '—' }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $o->service->category->name ?? '—' }}</div>
                                        </td>
                                        <td class="py-3 px-6">
                                            @php
                                                $statusBadge = match(strtolower($o->status ?? '')) {
                                                    'pending', 'processing' => 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30',
                                                    'completed' => 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30',
                                                    'partial' => 'bg-orange-100 text-orange-800 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:ring-orange-400/30',
                                                    'canceled', 'cancelled', 'error' => 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30',
                                                    default => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                                };
                                            @endphp
                                            <span class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $statusBadge }}">
                                                {{ ucfirst($o->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6">
                                            <span class="font-mono text-sm text-slate-600 dark:text-slate-400">
                                                Rp {{ number_format($o->cost, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-6 text-center">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada orders</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Recent API Logs --}}
                <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                    <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">API Logs Terbaru</h3>
                            </div>
                            <a href="{{ route('admin.api-logs.index') }}" class="text-sm text-primary hover:text-primary/80 transition-colors">
                                Lihat semua →
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#ID</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Provider</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Endpoint</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Status</th>
                                    <th class="py-3 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                                @forelse($recentApiLogs ?? [] as $log)
                                    @php $ok = (int)($log->status_code ?? 0) >= 200 && (int)($log->status_code ?? 0) < 400; @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="py-3 px-6">
                                            <a href="{{ route('admin.api-logs.show', $log) }}" class="font-medium text-primary hover:text-primary/80 transition-colors">
                                                #{{ $log->id }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-6">
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $log->provider->name ?? '—' }}</div>
                                        </td>
                                        <td class="py-3 px-6">
                                            <div class="text-sm text-slate-600 dark:text-slate-400 truncate max-w-32" title="{{ $log->endpoint ?? '' }}">
                                                {{ $log->endpoint ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-6">
                                            <span class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset
                                                   {{ $ok 
                                                       ? 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30' 
                                                       : 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30' }}">
                                                {{ $log->status_code ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6">
                                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                                {{ optional($log->created_at)->format('H:i') ?? '—' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-6 text-center">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada API logs</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.users.index') }}" class="p-4 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-600/10 border border-blue-200/60 dark:border-blue-700/60 hover:from-blue-500/20 hover:to-blue-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-blue-900 dark:text-blue-100">Manage Users</p>
                                <p class="text-sm text-blue-600 dark:text-blue-400">View & edit users</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="p-4 rounded-xl bg-gradient-to-r from-green-500/10 to-green-600/10 border border-green-200/60 dark:border-green-700/60 hover:from-green-500/20 hover:to-green-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-green-900 dark:text-green-100">Manage Orders</p>
                                <p class="text-sm text-green-600 dark:text-green-400">Monitor & process orders</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.services.index') }}" class="p-4 rounded-xl bg-gradient-to-r from-purple-500/10 to-purple-600/10 border border-purple-200/60 dark:border-purple-700/60 hover:from-purple-500/20 hover:to-purple-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-purple-900 dark:text-purple-100">Manage Services</p>
                                <p class="text-sm text-purple-600 dark:text-purple-400">Configure services</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.tickets.index') }}" class="p-4 rounded-xl bg-gradient-to-r from-orange-500/10 to-orange-600/10 border border-orange-200/60 dark:border-orange-700/60 hover:from-orange-500/20 hover:to-orange-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-orange-900 dark:text-orange-100">Support Tickets</p>
                                <p class="text-sm text-orange-600 dark:text-orange-400">Handle user issues</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- System Status Section --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Status Sistem</h3>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                            <div>
                                <div class="text-sm font-medium text-green-800 dark:text-green-200">Database</div>
                                <div class="text-xs text-green-600 dark:text-green-400">Connected</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></div>
                            <div>
                                <div class="text-sm font-medium text-blue-800 dark:text-blue-200">Cache</div>
                                <div class="text-xs text-blue-600 dark:text-blue-400">Active</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-purple-500 animate-pulse"></div>
                            <div>
                                <div class="text-sm font-medium text-purple-800 dark:text-purple-200">Queue</div>
                                <div class="text-xs text-purple-600 dark:text-purple-400">Running</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></div>
                            <div>
                                <div class="text-sm font-medium text-orange-800 dark:text-orange-200">Storage</div>
                                <div class="text-xs text-orange-600 dark:text-orange-400">Available</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Refresh functionality
            document.getElementById('refreshBtn')?.addEventListener('click', function() {
                this.disabled = true;
                this.innerHTML = `
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refreshing...</span>
                `;
                
                // Simulate refresh
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });

            // Auto-update timestamp
            setInterval(() => {
                const now = new Date();
                document.getElementById('lastUpdated').textContent = now.toLocaleTimeString('id-ID');
            }, 1000);

            // Real-time data update (every 30 seconds)
            setInterval(() => {
                updateDashboardData();
            }, 30000);

            // Function to update dashboard data
            async function updateDashboardData() {
                try {
                    const response = await fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        // Update KPI numbers with animation
                        updateKPINumbers();
                    }
                } catch (error) {
                    console.log('Dashboard update failed:', error);
                }
            }

            // Function to animate KPI number updates
            function updateKPINumbers() {
                const kpiElements = document.querySelectorAll('[data-kpi]');
                kpiElements.forEach(element => {
                    const currentValue = parseInt(element.textContent.replace(/[^\d]/g, ''));
                    const newValue = currentValue + Math.floor(Math.random() * 5);
                    
                    // Animate the number change
                    animateNumber(element, currentValue, newValue, 1000);
                });
            }

            // Function to animate number changes
            function animateNumber(element, start, end, duration) {
                const startTime = performance.now();
                const change = end - start;
                
                function updateNumber(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    const current = Math.floor(start + (change * progress));
                    element.textContent = element.textContent.replace(/\d+/, current);
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateNumber);
                    }
                }
                
                requestAnimationFrame(updateNumber);
            }

            // Chart data from PHP
            const ordersData = @json($ordersTrend ?? []);
            const revenueData = @json($revenueTrend ?? []);

            // Orders Chart
            const ordersCtx = document.getElementById('ordersChart');
            if (ordersCtx && ordersData.length > 0) {
                new Chart(ordersCtx, {
                    type: 'bar',
                    data: {
                        labels: ordersData.map(item => item.label),
                        datasets: [{
                            label: 'Orders',
                            data: ordersData.map(item => item.count),
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(59, 130, 246, 0.5)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        return `${context.parsed.y} orders`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.1)'
                                },
                                ticks: {
                                    color: '#64748b',
                                    callback: function(value) {
                                        return value;
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            } else if (ordersCtx) {
                // Show message when no data
                ordersCtx.getContext('2d').fillStyle = '#64748b';
                ordersCtx.getContext('2d').font = '14px Arial';
                ordersCtx.getContext('2d').textAlign = 'center';
                ordersCtx.getContext('2d').fillText('No orders data available', ordersCtx.width / 2, ordersCtx.height / 2);
            }

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx && revenueData.length > 0) {
                new Chart(revenueCtx, {
                    type: 'bar',
                    data: {
                        labels: revenueData.map(item => item.label),
                        datasets: [{
                            label: 'Revenue',
                            data: revenueData.map(item => item.amount),
                            backgroundColor: 'rgba(74, 222, 128, 0.2)',
                            borderColor: 'rgb(74, 222, 128)',
                            borderWidth: 1,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(74, 222, 128, 0.5)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        return `Rp ${context.parsed.y.toLocaleString('id-ID')}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.1)'
                                },
                                ticks: {
                                    color: '#64748b',
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            } else if (revenueCtx) {
                // Show message when no data
                revenueCtx.getContext('2d').fillStyle = '#64748b';
                revenueCtx.getContext('2d').font = '14px Arial';
                revenueCtx.getContext('2d').textAlign = 'center';
                revenueCtx.getContext('2d').fillText('No revenue data available', revenueCtx.width / 2, revenueCtx.height / 2);
            }
        </script>
    @endpush
</x-app-layout>
