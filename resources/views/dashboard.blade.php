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
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Welcome Banner --}}
            <div
                class="p-6 rounded-2xl bg-gradient-to-r from-primary/10 to-purple-600/10 border border-primary/20 dark:border-primary/30">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-primary/20 text-primary">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Selamat Datang!</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Pantau saldo wallet, pesanan, dan aktivitas terbaru Anda
                        </p>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Wallet Card --}}
                <div
                    class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Saldo Wallet</div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('wallet.topup') }}"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Top-up
                        </a>
                        <a href="{{ route('wallet.transactions') }}"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z" />
                            </svg>
                            Riwayat Wallet
                        </a>
                    </div>
                </div>

                {{-- Pending Orders Card --}}
                <div
                    class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-3 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Pesanan Pending
                                </div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                    {{ \App\Models\Order::where('user_id', auth()->id())->where('status', 'pending')->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('orders.index') }}"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat Semua
                        </a>
                    </div>
                </div>

                {{-- Total Orders Card --}}
                <div
                    class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Pesanan</div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                    {{ \App\Models\Order::where('user_id', auth()->id())->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('services.index') }}"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                            </svg>

                            Order Baru
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pesanan Terbaru</h3>
                    </div>
                    <a href="{{ route('orders.index') }}"
                        class="text-sm text-primary hover:text-primary/80 transition-colors">Lihat Semua →</a>
                </div>

                @php
                    $latestOrders = \App\Models\Order::with('service')
                        ->where('user_id', auth()->id())
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                @if ($latestOrders->isEmpty())
                    <div class="text-center py-8">
                        <div
                            class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada pesanan</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Mulai order layanan untuk melihat
                            riwayat di sini</p>
                        <a href="{{ route('services.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Order Sekarang
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-200/60 dark:border-slate-700/60">
                                    <th
                                        class="text-left py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                        Layanan</th>
                                    <th
                                        class="text-left py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                        Qty</th>
                                    <th
                                        class="text-left py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                        Biaya</th>
                                    <th
                                        class="text-left py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                        Status</th>
                                    <th
                                        class="text-left py-3 px-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                                        Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                                @foreach ($latestOrders as $order)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-primary" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="font-medium text-slate-900 dark:text-white">{{ $order->service->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                            {{ number_format($order->quantity) }}</td>
                                        <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">Rp
                                            {{ number_format($order->cost, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4">
                                            @php
                                                $st = strtolower($order->status ?? '');
                                                switch ($st) {
                                                    case 'pending':
                                                        $badgeClasses =
                                                            'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30';
                                                        break;
                                                    case 'processing':
                                                        $badgeClasses =
                                                            'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30';
                                                        break;
                                                    case 'completed':
                                                        $badgeClasses =
                                                            'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30';
                                                        break;
                                                    case 'partial':
                                                        $badgeClasses =
                                                            'bg-orange-100 text-orange-800 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:ring-orange-400/30';
                                                        break;
                                                    case 'canceled':
                                                    case 'cancelled':
                                                        $badgeClasses =
                                                            'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30';
                                                        break;
                                                    case 'error':
                                                        $badgeClasses =
                                                            'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30';
                                                        break;
                                                    default:
                                                        $badgeClasses =
                                                            'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30';
                                                }
                                            @endphp
                                            <span
                                                class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                                                {{ ucfirst($st) ?: 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $order->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
