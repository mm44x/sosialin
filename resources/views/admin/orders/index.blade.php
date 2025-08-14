{{-- Path : resources/views/admin/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Orders</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FILTER BAR --}}
            <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium">Cari (ID/Provider ID/Link/Email/Nama)</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                        placeholder="mis. 1024 atau user@example.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Status</label>
                    @php $st = $filters['status'] ?? ''; @endphp
                    <select name="status"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                   bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                        <option value="">Semua</option>
                        @foreach (['pending', 'processing', 'completed', 'partial', 'canceled', 'error'] as $opt)
                            <option value="{{ $opt }}" @selected($st === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium">Per halaman</label>
                    @php $pp = (int)($filters['per_page'] ?? 20); @endphp
                    <select name="per_page"
                        class="mt-1 h-10 px-3 py-2 rounded-xl border
                                   bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600
                                   w-24 md:w-24">
                        @foreach ([10, 20, 30, 50] as $opt)
                            <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Dari tanggal</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Sampai tanggal</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>
                <div class="md:col-span-12">
                    <div class="flex justify-end gap-2 flex-wrap md:flex-nowrap">
                        <button class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                        <a href="{{ route('admin.orders.index') }}"
                            class="h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 flex items-center justify-center">Reset</a>
                        <a href="{{ route('admin.orders.export', request()->query()) }}"
                            class="h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 flex items-center justify-center">Export</a>
                    </div>
                </div>
            </form>

            {{-- BULK ACTION BAR --}}
            <div class="mb-2 flex justify-end">
                <form id="bulkForm" method="POST" action="{{ route('admin.orders.bulk-status-check') }}"
                    class="flex gap-2">
                    @csrf
                    <input type="hidden" name="ids" id="bulkIds">
                    <button type="submit" id="bulkBtn"
                        class="h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 disabled:opacity-50"
                        disabled>
                        Cek status terpilih
                    </button>
                </form>
            </div>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">
                                <input id="selectAll" type="checkbox" class="h-4 w-4 accent-primary cursor-pointer">
                            </th>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">User</th>
                            <th class="py-2 px-4">Layanan</th>
                            <th class="py-2 px-4">Qty</th>
                            <th class="py-2 px-4">Biaya</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Prov.ID</th>
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $o)
                            @php
                                $st = strtolower($o->status ?? '');
                                $isFinal = in_array($st, ['completed', 'error'], true);
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">
                                    <div class="flex items-center gap-1">
                                        <input type="checkbox"
                                            class="h-4 w-4 js-row-check transition
                              {{ $isFinal
                                  ? 'accent-slate-400 opacity-40 cursor-not-allowed'
                                  : 'accent-primary cursor-pointer hover:scale-105' }}"
                                            value="{{ $o->id }}" @disabled($isFinal)
                                            aria-disabled="{{ $isFinal ? 'true' : 'false' }}"
                                            @if ($isFinal) title="Status final — tidak bisa bulk check" @endif>
                                        @if ($isFinal)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400"
                                                viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 016 0v3H9z" />
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="flex items-center gap-2">
                                        <span>#{{ $o->id }}</span>
                                        <button type="button"
                                            class="p-1 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary js-copy"
                                            data-copy="{{ $o->id }}" title="Salin Order ID">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $o->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $o->user->email ?? '' }}</div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">
                                        {{ $o->service->public_name ?? ($o->service->name ?? '-') }}</div>
                                    <div class="text-xs text-slate-500">{{ $o->service->category->name ?? '—' }}</div>
                                </td>
                                <td class="py-2 px-4">{{ number_format($o->quantity) }}</td>
                                <td class="py-2 px-4">Rp {{ number_format($o->cost, 2) }}</td>
                                <td class="py-2 px-4">
                                    <span @class([
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        'bg-yellow-100 text-yellow-800 ring-yellow-200' => in_array($st, [
                                            'pending',
                                            'processing',
                                        ]),
                                        'bg-green-100 text-green-800 ring-green-200' => $st === 'completed',
                                        'bg-orange-100 text-orange-800 ring-orange-200' => $st === 'partial',
                                        'bg-red-100 text-red-800 ring-red-200' => in_array($st, [
                                            'canceled',
                                            'cancelled',
                                            'error',
                                        ]),
                                        'bg-slate-100 text-slate-800 ring-slate-200' => !in_array($st, [
                                            'pending',
                                            'processing',
                                            'completed',
                                            'partial',
                                            'canceled',
                                            'cancelled',
                                            'error',
                                        ]),
                                    ])>{{ ucfirst($st) ?: 'Unknown' }}</span>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $o->provider_order_id ?? '—' }}</span>
                                        @if ($o->provider_order_id)
                                            <button type="button" class="p-1 rounded-lg hover:bg-primary/10 js-copy"
                                                data-copy="{{ $o->provider_order_id }}"
                                                title="Salin Provider Order ID">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2 px-4">{{ $o->updated_at?->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.orders.show', $o) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>
                                        @if (in_array($st, ['pending', 'processing', 'partial']) && $o->provider_order_id)
                                            <form method="POST"
                                                action="{{ route('admin.orders.status-check', $o) }}">
                                                @csrf
                                                <button
                                                    class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                                    Cek status
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="10">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $rows->links() }}</div>
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
