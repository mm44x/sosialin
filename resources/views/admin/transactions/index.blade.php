<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Manajemen Transaksi
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Kelola dan pantau semua transaksi keuangan pengguna
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Section --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Filter & Pencarian</h3>
                </div>

                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Cari (ID/Email/Nama)
                            </label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="mis. 123 atau user@example.com">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe</label>
                            @php $tp = $filters['type'] ?? ''; @endphp
                            <select name="type"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="">Semua Tipe</option>
                                @foreach (['topup', 'order', 'refund'] as $opt)
                                    <option value="{{ $opt }}" @selected($tp === $opt)>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Min Amount</label>
                            <input type="number" step="0.01" name="min" value="{{ $filters['min'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="0.00">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Max Amount</label>
                            <input type="number" step="0.01" name="max" value="{{ $filters['max'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="0.00">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Dari Tanggal</label>
                            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sampai Tanggal</label>
                            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Per Halaman</label>
                            @php $pp = (int)($filters['per_page'] ?? 20); @endphp
                            <select name="per_page"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                @foreach ([10, 20, 30, 50] as $opt)
                                    <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('admin.transactions.index') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                  border border-slate-200 dark:border-slate-700
                                  hover:border-primary/20 dark:hover:border-primary/20 
                                  hover:bg-primary/5 dark:hover:bg-primary/5
                                  text-slate-700 dark:text-slate-300
                                  transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reset Filter</span>
                        </a>
                        <a href="{{ route('admin.transactions.export', request()->query()) }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                  border border-slate-200 dark:border-slate-700
                                  hover:border-primary/20 dark:hover:border-primary/20 
                                  hover:bg-primary/5 dark:hover:bg-primary/5
                                  text-slate-700 dark:text-slate-300
                                  transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export CSV</span>
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                       bg-gradient-to-r from-primary to-purple-600 
                                       text-white font-medium shadow-sm 
                                       hover:shadow-md transition-all duration-300 hover:scale-105
                                       focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Terapkan Filter</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Transactions Table --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Transaksi</h3>
                        <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                            {{ $rows->total() }} transaksi
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">User</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Tipe</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Amount</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Meta</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse ($rows as $t)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-sm text-slate-600 dark:text-slate-400">#{{ $t->id }}</span>
                                            <button type="button"
                                                class="p-1.5 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary/20 js-copy transition-colors"
                                                data-copy="{{ $t->id }}" title="Salin Transaction ID">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $t->user->name ?? '—' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t->user->email ?? '' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @php
                                            $typeBadge = match($t->type) {
                                                'topup' => 'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30',
                                                'order' => 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30',
                                                'refund' => 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30',
                                                default => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset {{ $typeBadge }}">
                                            {{ ucfirst($t->type) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @php
                                            $amt = (float) $t->amount;
                                            $isNeg = $amt < 0;
                                            $abs = abs($amt);
                                        @endphp
                                        <span class="font-mono text-sm {{ $isNeg ? 'text-red-700 dark:text-red-400' : 'text-slate-900 dark:text-slate-100' }}">
                                            {!! $isNeg ? '−' : '' !!}Rp {{ number_format($abs, 2) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 max-w-[28rem]">
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 max-h-32 overflow-y-auto">
                                            <pre class="text-xs text-slate-600 dark:text-slate-400 whitespace-pre-wrap">{{ json_encode($t->meta ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $t->created_at?->diffForHumans() }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada transaksi</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada transaksi yang ditemukan dengan filter saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($rows->hasPages())
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mt-6 px-4 sm:px-0">
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            // Copy helper (reusable)
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
                    const ta = document.createElement('textarea');
                    ta.value = val;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        ok = document.execCommand('copy');
                    } catch {}
                    document.body.removeChild(ta);
                }
                const old = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = ok ?
                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                setTimeout(() => {
                    btn.innerHTML = old;
                    btn.disabled = false;
                }, 900);
                if (window.toast) window.toast({
                    message: ok ? 'Disalin.' : 'Gagal menyalin',
                    type: ok ? 'success' : 'error',
                    timeout: 1200
                });
            });
        </script>
    @endpush
</x-app-layout>
