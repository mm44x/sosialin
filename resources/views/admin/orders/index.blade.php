{{-- Path : resources/views/admin/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Manajemen Orders
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Kelola dan pantau semua orders dari pengguna
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
                                Cari (ID/Provider ID/Link/Email/Nama)
                            </label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="mis. 1024 atau user@example.com">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                            @php $st = $filters['status'] ?? ''; @endphp
                            <select name="status"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="">Semua Status</option>
                                @foreach (['pending', 'processing', 'completed', 'partial', 'canceled', 'error'] as $opt)
                                    <option value="{{ $opt }}" @selected($st === $opt)>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
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
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('admin.orders.index') }}"
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
                        <a href="{{ route('admin.orders.export', request()->query()) }}"
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

            {{-- Bulk Action Bar --}}
            <div class="flex justify-end">
                <form id="bulkForm" method="POST" action="{{ route('admin.orders.bulk-status-check') }}"
                    class="inline-flex">
                    @csrf
                    <input type="hidden" name="ids" id="bulkIds">
                    <button type="submit" id="bulkBtn"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                               border border-slate-200 dark:border-slate-700
                               hover:border-primary/20 dark:hover:border-primary/20 
                               hover:bg-primary/5 dark:hover:bg-primary/5
                               text-slate-700 dark:text-slate-300
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition-all duration-300"
                        disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Cek Status Terpilih</span>
                    </button>
                </form>
            </div>

            {{-- Orders Table --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Orders</h3>
                        <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                            {{ $rows->total() }} orders
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left">
                                    <input id="selectAll" type="checkbox" class="h-4 w-4 accent-primary cursor-pointer">
                                </th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">User</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Layanan</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Qty</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Biaya</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Status</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Prov.ID</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Updated</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse ($rows as $o)
                                @php
                                    $st = strtolower($o->status ?? '');
                                    $isFinal = in_array($st, ['completed', 'error'], true);
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox"
                                                class="h-4 w-4 js-row-check transition
                                                      {{ $isFinal
                                                          ? 'accent-slate-400 opacity-40 cursor-not-allowed'
                                                          : 'accent-primary cursor-pointer hover:scale-105' }}"
                                                value="{{ $o->id }}" @disabled($isFinal)
                                                aria-disabled="{{ $isFinal ? 'true' : 'false' }}"
                                                @if ($isFinal) title="Status final — tidak bisa bulk check" @endif>
                                            @if ($isFinal)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400"
                                                    viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 016 0v3H9z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-sm text-slate-600 dark:text-slate-400">#{{ $o->id }}</span>
                                            <button type="button"
                                                class="p-1.5 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary/20 js-copy transition-colors"
                                                data-copy="{{ $o->id }}" title="Salin Order ID">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $o->user->name ?? '—' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $o->user->email ?? '' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $o->service->public_name ?? ($o->service->name ?? '-') }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $o->service->category->name ?? '—' }}</div>
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
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-sm text-slate-600 dark:text-slate-400">{{ $o->provider_order_id ?? '—' }}</span>
                                            @if ($o->provider_order_id)
                                                <button type="button" class="p-1.5 rounded-lg hover:bg-primary/10 js-copy transition-colors"
                                                    data-copy="{{ $o->provider_order_id }}"
                                                    title="Salin Provider Order ID">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $o->updated_at?->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex gap-2">
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
                                            @if (in_array($st, ['pending', 'processing', 'partial']) && $o->provider_order_id)
                                                <form method="POST"
                                                    action="{{ route('admin.orders.status-check', $o) }}">
                                                    @csrf
                                                    <button
                                                        class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl 
                                                               border border-slate-200 dark:border-slate-700
                                                               hover:border-primary/20 dark:hover:border-primary/20 
                                                               hover:bg-primary/5 dark:hover:bg-primary/5
                                                               text-slate-700 dark:text-slate-300
                                                               transition-all duration-300">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>Cek Status</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada orders</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada orders yang ditemukan dengan filter saat ini.</p>
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
            // Copy (order id / provider id) — satu handler
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.js-copy');
                if (!btn) return;
                const text = btn.getAttribute('data-copy') || '';
                let ok = false;
                if (navigator.clipboard && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(text);
                        ok = true;
                    } catch {}
                }
                if (!ok) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
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
                if (window.toast) window.toast({
                    message: ok ? 'Disalin.' : 'Gagal menyalin',
                    type: ok ? 'success' : 'error',
                    timeout: 1200
                });
                setTimeout(() => {
                    btn.innerHTML = old;
                    btn.disabled = false;
                }, 900);
            });

            // Bulk select + submit
            document.addEventListener('DOMContentLoaded', () => {
                const selectAll = document.getElementById('selectAll');
                const bulkBtn = document.getElementById('bulkBtn');
                const bulkIds = document.getElementById('bulkIds');
                const bulkForm = document.getElementById('bulkForm');

                const checks = () => Array.from(document.querySelectorAll('.js-row-check:not(:disabled)'));

                function updateBulkState() {
                    const selected = checks().filter(c => c.checked).length;
                    if (bulkBtn) bulkBtn.disabled = selected === 0;
                }

                if (selectAll) {
                    selectAll.addEventListener('change', () => {
                        checks().forEach(c => c.checked = selectAll.checked);
                        updateBulkState();
                    });
                }
                document.addEventListener('change', (e) => {
                    if (e.target.classList && e.target.classList.contains('js-row-check')) {
                        updateBulkState();
                    }
                });

                if (bulkForm) {
                    bulkForm.addEventListener('submit', (e) => {
                        const selected = checks().filter(c => c.checked).map(c => c.value);
                        if (selected.length === 0) {
                            e.preventDefault();
                            if (window.toast) window.toast({
                                message: 'Pilih minimal satu order.',
                                type: 'error',
                                timeout: 1200
                            });
                            return;
                        }
                        bulkIds.value = selected.join(',');
                    });
                }

                // set awal
                updateBulkState();
            });
        </script>
    @endpush
</x-app-layout>
