<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Dashboard
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Selamat datang kembali, {{ auth()->user()->name }}! Pantau aktivitas dan saldo Anda di sini.
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
            {{-- Notifications Section --}}
            @if(isset($notifications) && count($notifications) > 0)
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        <div class="p-4 rounded-xl border-l-4 {{ $notification['type'] === 'warning' ? 'bg-yellow-50 border-yellow-400 dark:bg-yellow-900/20 dark:border-yellow-500' : 'bg-blue-50 border-blue-400 dark:bg-blue-900/20 dark:border-blue-500' }}">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @if($notification['icon'] === 'wallet')
                                        <svg class="w-5 h-5 {{ $notification['type'] === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    @elseif($notification['icon'] === 'clock')
                                        <svg class="w-5 h-5 {{ $notification['type'] === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 {{ $notification['type'] === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium {{ $notification['type'] === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-blue-800 dark:text-blue-200' }}">
                                        {{ $notification['title'] }}
                                    </h4>
                                    <p class="mt-1 text-sm {{ $notification['type'] === 'warning' ? 'text-yellow-700 dark:text-yellow-300' : 'text-blue-700 dark:text-blue-300' }}">
                                        {{ $notification['message'] }}
                                    </p>
                                </div>
                                @if(isset($notification['action']))
                                    <div class="flex-shrink-0">
                                        <a href="{{ $notification['action'] }}" class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $notification['type'] === 'warning' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200 dark:hover:bg-yellow-700' : 'bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700' }} transition-colors">
                                            {{ $notification['action_text'] }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Welcome Banner with Quick Stats --}}
            <div class="p-6 rounded-2xl bg-gradient-to-r from-primary/10 to-purple-600/10 border border-primary/20 dark:border-primary/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-xl bg-primary/20 text-primary">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Selamat Datang!</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                Pantau saldo wallet, pesanan, dan aktivitas terbaru Anda
                            </p>
                        </div>
                    </div>
                    @if(isset($quickStats))
                        <div class="hidden md:flex items-center gap-6 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ number_format($quickStats['success_rate'], 1) }}%</div>
                                <div class="text-slate-500 dark:text-slate-400">Success Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-slate-900 dark:text-white">Rp {{ number_format($quickStats['avg_order_value'], 0, ',', '.') }}</div>
                                <div class="text-slate-500 dark:text-slate-400">Avg Order</div>
                            </div>
                            @if($quickStats['days_since_last_order'] !== null)
                                <div class="text-center">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $quickStats['days_since_last_order'] }}</div>
                                    <div class="text-slate-500 dark:text-slate-400">Days Since Last Order</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Wallet Card --}}
                <div class="p-6 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Saldo Wallet</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($balance ?? 0, 0, ',', '.') }}</p>
                            @if(isset($transactionStats))
                                <p class="text-green-100 text-xs mt-1">+{{ number_format($transactionStats['this_month_topup'], 0, ',', '.') }} bulan ini</p>
                            @endif
                        </div>
                        <div class="p-3 rounded-xl bg-green-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('wallet.topup') }}" class="flex-1 text-center px-3 py-2 rounded-xl bg-green-400/20 text-white text-sm font-medium hover:bg-green-400/30 transition-colors">
                            Top-up
                        </a>
                        <a href="{{ route('wallet.transactions') }}" class="flex-1 text-center px-3 py-2 rounded-xl bg-white/20 text-white text-sm font-medium hover:bg-white/30 transition-colors">
                            Riwayat
                        </a>
                    </div>
                </div>

                {{-- Orders Card --}}
                <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Orders</p>
                            <p class="text-2xl font-bold">{{ $orderStats['total'] ?? 0 }}</p>
                            @if(isset($orderStats))
                                <p class="text-blue-100 text-xs mt-1">{{ $orderStats['this_month'] }} bulan ini</p>
                            @endif
                        </div>
                        <div class="p-3 rounded-xl bg-blue-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('orders.index') }}" class="flex-1 text-center px-3 py-2 rounded-xl bg-blue-400/20 text-white text-sm font-medium hover:bg-blue-400/30 transition-colors">
                            Lihat Orders
                        </a>
                        <a href="{{ route('services.index') }}" class="flex-1 text-center px-3 py-2 rounded-xl bg-white/20 text-white text-sm font-medium hover:bg-white/30 transition-colors">
                            Order Baru
                        </a>
                    </div>
                </div>

                {{-- Pending Orders Card --}}
                <div class="p-6 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Pending Orders</p>
                            <p class="text-2xl font-bold">{{ $orderStats['pending'] ?? 0 }}</p>
                            @if(isset($orderStats))
                                <p class="text-yellow-100 text-xs mt-1">{{ $orderStats['processing'] }} processing</p>
                            @endif
                        </div>
                        <div class="p-3 rounded-xl bg-yellow-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('orders.index') }}" class="w-full text-center px-3 py-2 rounded-xl bg-yellow-400/20 text-white text-sm font-medium hover:bg-yellow-400/30 transition-colors">
                            Monitor Orders
                        </a>
                    </div>
                </div>

                {{-- Total Spent Card --}}
                <div class="p-6 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Spent</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($quickStats['total_spent'] ?? 0, 0, ',', '.') }}</p>
                            @if(isset($transactionStats))
                                <p class="text-purple-100 text-xs mt-1">Rp {{ number_format($transactionStats['this_month_spent'], 0, ',', '.') }} bulan ini</p>
                            @endif
                        </div>
                        <div class="p-3 rounded-xl bg-purple-400/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('wallet.transactions') }}" class="w-full text-center px-3 py-2 rounded-xl bg-purple-400/20 text-white text-sm font-medium hover:bg-purple-400/30 transition-colors">
                            Lihat Transaksi
                        </a>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            @if(isset($chartData) && count($chartData) > 0)
                <div class="grid lg:grid-cols-2 gap-6">
                    {{-- Orders Chart --}}
                    <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Aktivitas 7 Hari Terakhir</h3>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Orders</span>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="ordersChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    {{-- Spending Chart --}}
                    <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pengeluaran 7 Hari Terakhir</h3>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Spending</span>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="spendingChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Content Grid --}}
            <div class="grid lg:grid-cols-3 gap-6">
                {{-- Recent Orders --}}
                <div class="lg:col-span-2 p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pesanan Terbaru</h3>
                        </div>
                        <a href="{{ route('orders.index') }}" class="text-sm text-primary hover:text-primary/80 transition-colors">Lihat Semua →</a>
                    </div>

                    @if (isset($recentOrders) && $recentOrders->count() > 0)
                        <div class="space-y-4">
                            @foreach ($recentOrders as $order)
                                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 dark:bg-slate-700/30 hover:bg-slate-100/50 dark:hover:bg-slate-600/30 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $order->service->name ?? 'Unknown Service' }}</div>
                                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $order->service->category->name ?? 'Uncategorized' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-slate-900 dark:text-white">Rp {{ number_format($order->cost, 0, ',', '.') }}</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">{{ number_format($order->quantity) }} qty</div>
                                        @php
                                            $statusBadge = match(strtolower($order->status ?? '')) {
                                                'pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30',
                                                'processing' => 'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30',
                                                'completed' => 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30',
                                                'partial' => 'bg-orange-100 text-orange-800 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:ring-orange-400/30',
                                                'canceled', 'cancelled', 'error' => 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30',
                                                default => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                            };
                                        @endphp
                                        <span class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $statusBadge }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada pesanan</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Mulai order layanan untuk melihat riwayat di sini</p>
                            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Order Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Recent Transactions --}}
                    <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Transaksi Terbaru</h3>
                            <a href="{{ route('wallet.transactions') }}" class="text-sm text-primary hover:text-primary/80 transition-colors">Lihat Semua</a>
                        </div>
                        @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentTransactions->take(5) as $transaction)
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/50 dark:bg-slate-700/30">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg {{ $transaction->type === 'topup' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} flex items-center justify-center">
                                                <svg class="w-4 h-4 {{ $transaction->type === 'topup' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $transaction->type === 'topup' ? 'M12 6v6m0 0v6m0-6h6m-6 0H6' : 'M20 12H4' }}" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900 dark:text-white">{{ ucfirst($transaction->type) }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $transaction->created_at->format('d M H:i') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium {{ $transaction->type === 'topup' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $transaction->type === 'topup' ? '+' : '-' }}Rp {{ number_format(abs($transaction->amount), 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada transaksi</p>
                            </div>
                        @endif
                    </div>

                    {{-- Recent Tickets --}}
                    <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Ticket Terbaru</h3>
                            <a href="{{ route('tickets.index') }}" class="text-sm text-primary hover:text-primary/80 transition-colors">Lihat Semua</a>
                        </div>
                        @if(isset($recentTickets) && $recentTickets->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentTickets as $ticket)
                                    <div class="p-3 rounded-lg bg-slate-50/50 dark:bg-slate-700/30">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ Str::limit($ticket->subject, 30) }}</div>
                                            <span class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30' : 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30' }}">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $ticket->created_at->format('d M H:i') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada ticket</p>
                            </div>
                        @endif
                    </div>

                    {{-- Popular Services --}}
                    @if(isset($popularServices) && $popularServices->count() > 0)
                        <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Layanan Populer</h3>
                            <div class="space-y-3">
                                @foreach($popularServices->take(5) as $service)
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50/50 dark:bg-slate-700/30">
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $service->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $service->category->name ?? 'Uncategorized' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $service->orders_count }} orders</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('services.index') }}" class="p-4 rounded-xl bg-gradient-to-r from-blue-500/10 to-blue-600/10 border border-blue-200/60 dark:border-blue-700/60 hover:from-blue-500/20 hover:to-blue-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-blue-900 dark:text-blue-100">Order Layanan</p>
                                <p class="text-sm text-blue-600 dark:text-blue-400">Buat pesanan baru</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('wallet.topup') }}" class="p-4 rounded-xl bg-gradient-to-r from-green-500/10 to-green-600/10 border border-green-200/60 dark:border-green-700/60 hover:from-green-500/20 hover:to-green-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-green-900 dark:text-green-100">Top-up Wallet</p>
                                <p class="text-sm text-green-600 dark:text-green-400">Tambah saldo</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('tickets.create') }}" class="p-4 rounded-xl bg-gradient-to-r from-purple-500/10 to-purple-600/10 border border-purple-200/60 dark:border-purple-700/60 hover:from-purple-500/20 hover:to-purple-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-purple-900 dark:text-purple-100">Buat Ticket</p>
                                <p class="text-sm text-purple-600 dark:text-purple-400">Butuh bantuan?</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="p-4 rounded-xl bg-gradient-to-r from-orange-500/10 to-orange-600/10 border border-orange-200/60 dark:border-orange-700/60 hover:from-orange-500/20 hover:to-orange-600/20 transition-all duration-300 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-orange-900 dark:text-orange-100">Edit Profile</p>
                                <p class="text-sm text-orange-600 dark:text-orange-400">Update informasi</p>
                            </div>
                        </div>
                    </a>
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
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            });

            // Auto-update timestamp
            setInterval(() => {
                const now = new Date();
                document.getElementById('lastUpdated').textContent = now.toLocaleTimeString('id-ID');
            }, 1000);

            // Chart data from PHP
            @if(isset($chartData) && count($chartData) > 0)
                const chartData = @json($chartData);
                
                // Orders Chart
                const ordersCtx = document.getElementById('ordersChart');
                if (ordersCtx) {
                    new Chart(ordersCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.map(item => item.date),
                            datasets: [{
                                label: 'Orders',
                                data: chartData.map(item => item.orders),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgb(59, 130, 246)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
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
                                        stepSize: 1
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }

                // Spending Chart
                const spendingCtx = document.getElementById('spendingChart');
                if (spendingCtx) {
                    new Chart(spendingCtx, {
                        type: 'bar',
                        data: {
                            labels: chartData.map(item => item.date),
                            datasets: [{
                                label: 'Spending',
                                data: chartData.map(item => item.spent),
                                backgroundColor: 'rgba(74, 222, 128, 0.8)',
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
                                        label: function(context) {
                                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
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
                }
            @endif
        </script>
    @endpush
</x-app-layout>
