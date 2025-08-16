<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Detail Pengguna #{{ $user->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Lihat detail lengkap dan aktivitas pengguna
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('admin.users.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Daftar Pengguna
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- User Summary Card --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 rounded-xl bg-primary/10 text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-slate-600 dark:text-slate-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Nama</div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60 break-all">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Email</div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $user->email }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Saldo</div>
                        <div class="font-bold text-lg text-slate-900 dark:text-white">Rp {{ number_format($balance, 2) }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Total Orders</div>
                        <div class="font-semibold text-lg text-slate-900 dark:text-white">{{ (int) ($user->orders_count ?? 0) }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
                               border border-slate-200 dark:border-slate-700
                               hover:border-primary/20 dark:hover:border-primary/20 
                               hover:bg-primary/5 dark:hover:bg-primary/5
                               text-slate-700 dark:text-slate-300
                               transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit User</span>
                    </a>
                    <a href="{{ route('admin.orders.index', ['q' => $user->email]) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
                               border border-slate-200 dark:border-slate-700
                               hover:border-primary/20 dark:hover:border-primary/20 
                               hover:bg-primary/5 dark:hover:bg-primary/5
                               text-slate-700 dark:text-slate-300
                               transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Lihat Semua Order</span>
                    </a>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Order Terbaru</h3>
                        </div>
                        <a href="{{ route('admin.orders.index', ['q' => $user->email]) }}"
                            class="text-sm text-primary hover:text-primary/80 transition-colors">
                            Lihat semua →
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Layanan</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Qty</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Biaya</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Status</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Updated</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse($recentOrders as $o)
                                @php $st = strtolower($o->status ?? ''); @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-sm text-slate-600 dark:text-slate-400">#{{ $o->id }}</span>
                                            <button type="button"
                                                class="p-1.5 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary/20 js-copy transition-colors"
                                                data-copy="{{ $o->id }}" title="Salin Order ID">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $o->service->public_name ?? ($o->service->name ?? '-') }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            {{ $o->service->category->name ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-slate-600 dark:text-slate-400">{{ number_format($o->quantity) }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-slate-600 dark:text-slate-400">
                                            Rp {{ number_format($o->cost, 2) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @php
                                            $statusBadge = match($st) {
                                                'pending', 'processing' => 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30',
                                                'completed' => 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30',
                                                'partial' => 'bg-orange-100 text-orange-800 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:ring-orange-400/30',
                                                'canceled', 'cancelled', 'error' => 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30',
                                                default => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset {{ $statusBadge }}">
                                            {{ ucfirst($st) ?: 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $o->updated_at?->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <a href="{{ route('admin.orders.show', $o) }}"
                                            class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl 
                                                   border border-slate-200 dark:border-slate-700
                                                   hover:border-primary/20 dark:hover:border-primary/20 
                                                   hover:bg-primary/5 dark:hover:bg-primary/5
                                                   text-slate-700 dark:text-slate-300
                                                   transition-all duration-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada order</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">User ini belum melakukan order apapun.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Wallet Transactions --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Transaksi Wallet Terbaru</h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Tipe</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Nominal</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Keterangan</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse($recentTx as $t)
                                @php
                                    $type = strtolower($t->type ?? '');
                                    $isCredit = in_array($type, ['credit', 'refund', 'deposit', 'bonus']);
                                    $amount = (float) ($t->amount ?? 0);
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-slate-600 dark:text-slate-400">{{ $t->id }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset
                                               {{ $isCredit 
                                                   ? 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30' 
                                                   : 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30' }}">
                                            {{ ucfirst($type) ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm {{ $isCredit ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                            {{ $isCredit ? '+' : '-' }} Rp {{ number_format(abs($amount), 2) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-slate-600 dark:text-slate-400">{{ $t->description ?? '—' }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $t->created_at?->diffForHumans() }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada transaksi</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">User ini belum melakukan transaksi wallet apapun.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            // Copy util (event delegation)
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.js-copy');
                if (!btn) return;
                const val = btn.getAttribute('data-copy') || '';
                let ok = false;

                if (navigator.clipboard && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(val);
                        ok = true;
                    } catch {}
                }
                if (!ok) {
                    try {
                        const ta = document.createElement('textarea');
                        ta.value = val;
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.select();
                        ok = document.execCommand('copy');
                        document.body.removeChild(ta);
                    } catch {}
                }

                if (window.toast) {
                    window.toast({
                        message: ok ? 'Disalin.' : 'Gagal menyalin',
                        type: ok ? 'success' : 'error',
                        timeout: 1200
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
