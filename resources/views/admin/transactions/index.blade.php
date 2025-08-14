<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Transactions</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter bar --}}
            <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-5">
                    <label class="block text-sm font-medium">Cari (ID/Email/Nama)</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                        placeholder="mis. 123 atau user@example.com">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Tipe</label>
                    @php $tp = $filters['type'] ?? ''; @endphp
                    <select name="type"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                   bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                        <option value="">Semua</option>
                        @foreach (['topup', 'order', 'refund'] as $opt)
                            <option value="{{ $opt }}" @selected($tp === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Min Amount</label>
                    <input type="number" step="0.01" name="min" value="{{ $filters['min'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Max Amount</label>
                    <input type="number" step="0.01" name="max" value="{{ $filters['max'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium">Dari (Tanggal)</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium">Sampai (Tanggal)</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                  bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Per halaman</label>
                    @php $pp = (int)($filters['per_page'] ?? 20); @endphp
                    <select name="per_page"
                        class="mt-1 h-10 px-3 py-2 rounded-xl border
                                   bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600 w-24">
                        @foreach ([10, 20, 30, 50] as $opt)
                            <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Aksi (label dummy supaya sejajar) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium invisible select-none">&nbsp;</label>
                    <div class="mt-1 flex items-center justify-end gap-2 flex-wrap md:flex-nowrap">
                        <button type="submit"
                            class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90 leading-none">
                            Terapkan
                        </button>

                        <a href="{{ route('admin.orders.index') }}"
                            class="inline-flex items-center justify-center h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 leading-none">
                            Reset
                        </a>

                        <a href="{{ route('admin.orders.export', request()->query()) }}"
                            class="inline-flex items-center justify-center h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 leading-none">
                            Export
                        </a>
                    </div>
                </div>
            </form>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">User</th>
                            <th class="py-2 px-4">Type</th>
                            <th class="py-2 px-4">Amount</th>
                            <th class="py-2 px-4">Meta</th>
                            <th class="py-2 px-4">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $t)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">
                                    <div class="flex items-center gap-2">
                                        <span>#{{ $t->id }}</span>
                                        <button type="button"
                                            class="p-1 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary js-copy"
                                            data-copy="{{ $t->id }}" title="Salin Transaction ID">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $t->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $t->user->email ?? '' }}</div>
                                </td>
                                <td class="py-2 px-4">
                                    <span @class([
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        'bg-blue-100 text-blue-800 ring-blue-200' => $t->type === 'topup',
                                        'bg-red-100 text-red-800 ring-red-200' => $t->type === 'order',
                                        'bg-green-100 text-green-800 ring-green-200' => $t->type === 'refund',
                                        'bg-slate-100 text-slate-800 ring-slate-200' => !in_array($t->type, [
                                            'topup',
                                            'order',
                                            'refund',
                                        ]),
                                    ])>
                                        {{ ucfirst($t->type) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">
                                    @php
                                        $amt = (float) $t->amount;
                                        $isNeg = $amt < 0;
                                        $abs = abs($amt);
                                    @endphp
                                    <span
                                        class="@if ($isNeg) text-red-700 dark:text-red-400 @else text-slate-900 dark:text-slate-100 @endif">
                                        {!! $isNeg ? '−' : '' !!}Rp {{ number_format($abs, 2) }}
                                        {{-- Kalau mau format Indonesia pakai: number_format($abs, 2, ',', '.') --}}
                                    </span>
                                </td>
                                <td class="py-2 px-4 max-w-[28rem]">
                                    <pre class="text-xs overflow-x-auto whitespace-pre-wrap">{{ json_encode($t->meta ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </td>
                                <td class="py-2 px-4">{{ $t->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="6">Belum ada data.</td>
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
