<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-light dark:text-dark leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-500/10 text-green-600 ring-1 ring-inset ring-green-500/20">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid md:grid-cols-3 gap-6">
                <div
                    class="p-6 rounded-2xl bg-card-light dark:bg-card-dark shadow-sm ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slate-500 dark:text-slate-400">Saldo Wallet</div>
                    <div class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                        Rp {{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('wallet.topup') }}"
                            class="px-3 py-2 rounded-xl bg-primary text-white focus:ring-2 focus:ring-primary">Top-up</a>
                        <a href="{{ route('wallet.transactions') }}"
                            class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:ring-2 focus:ring-primary">Riwayat</a>
                        <a href="{{ route('services.index') }}"
                            class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-bgDark">
                            Lihat Layanan
                        </a>
                    </div>
                </div>

                <div
                    class="p-6 rounded-2xl bg-card-light dark:bg-card-dark shadow-sm ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slate-500 dark:text-slate-400">Pesanan Pending</div>
                    <div class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                        {{ \App\Models\Order::where('user_id', auth()->id())->where('status', 'pending')->count() }}
                    </div>
                </div>

                <div
                    class="p-6 rounded-2xl bg-card-light dark:bg-card-dark shadow-sm ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slate-500 dark:text-slate-400">Total Pesanan</div>
                    <div class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                        {{ \App\Models\Order::where('user_id', auth()->id())->count() }}
                    </div>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div
                class="p-6 rounded-2xl bg-card-light dark:bg-card-dark shadow-sm ring-1 ring-slate-200/60 dark:ring-white/10">
                <h3 class="font-semibold text-slate-800 dark:text-white">Pesanan Terbaru</h3>
                @php
                    $latestOrders = \App\Models\Order::with('service')
                        ->where('user_id', auth()->id())
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp
                @if ($latestOrders->isEmpty())
                    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Belum ada pesanan.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="text-slate-500 dark:text-slate-400">
                                <tr>
                                    <th class="p-2">Layanan</th>
                                    <th class="p-2">Qty</th>
                                    <th class="p-2">Biaya</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="text-slate-600 dark:text-slate-300">
                                @foreach ($latestOrders as $order)
                                    <tr class="border-t border-slate-200/60 dark:border-white/10">
                                        <td class="p-2">{{ $order->service->name ?? '-' }}</td>
                                        <td class="p-2">{{ $order->quantity }}</td>
                                        <td class="p-2">Rp {{ number_format($order->cost, 0, ',', '.') }}</td>
                                        <td class="p-2">
                                            <span
                                                class="px-2 py-1 rounded-md text-xs font-medium
                                                @switch($order->status)
                                                    @case('pending') bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 @break
                                                    @case('processing') bg-blue-500/10 text-blue-600 dark:text-blue-400 @break
                                                    @case('completed') bg-green-500/10 text-green-600 dark:text-green-400 @break
                                                    @case('partial') bg-orange-500/10 text-orange-600 dark:text-orange-400 @break
                                                    @case('canceled') bg-red-500/10 text-red-600 dark:text-red-400 @break
                                                    @case('error') bg-red-500/10 text-red-600 dark:text-red-400 @break
                                                @endswitch">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="p-2">{{ $order->created_at->format('d M Y H:i') }}</td>
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
