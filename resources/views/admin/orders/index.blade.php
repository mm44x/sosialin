<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Orders</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter bar --}}
            <form method="GET" class="mb-4 grid md:grid-cols-4 gap-3 md:items-end">
                @php
                    $ctl =
                        'mt-1 w-full h-11 px-3 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600';
                    $btn =
                        'h-11 inline-flex items-center px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10';
                    $btnPrimary =
                        'h-11 inline-flex items-center px-4 rounded-xl bg-primary text-white hover:opacity-90';
                @endphp

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Cari (ID/Provider ID/Link/Email/Nama)</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="{{ $ctl }}"
                        placeholder="mis. 1024 atau user@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium">Status</label>
                    <select name="status" class="{{ $ctl }}">
                        @php $st = $filters['status'] ?? ''; @endphp
                        <option value="">Semua</option>
                        @foreach (['pending', 'processing', 'completed', 'partial', 'canceled', 'error'] as $opt)
                            <option value="{{ $opt }}" @selected($st === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 md:justify-end self-end">
                    <button class="{{ $btnPrimary }}" type="submit">Terapkan</button>

                    <a href="{{ route('admin.orders.index') }}" class="{{ $btn }}">Reset</a>

                    <a href="{{ route('admin.orders.export', request()->query()) }}" class="{{ $btn }}"
                        title="Ekspor CSV sesuai filter saat ini">
                        Export
                    </a>
                </div>
            </form>



            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
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
                            @php $st = strtolower($o->status ?? ''); @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">
                                    <div class="flex items-center gap-2">
                                        <span>#{{ $o->id }}</span>
                                        <button type="button"
                                            class="p-1 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary js-copy-id"
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
                                        {{ $o->service->public_name ?? ($o->service->name ?? '-') }}
                                    </div>
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
                                <td class="py-2 px-4">{{ $o->provider_order_id ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $o->updated_at?->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.orders.show', $o) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>

                                        @if (in_array($st, ['pending', 'processing', 'partial']) && $o->provider_order_id)
                                            <form method="POST" action="{{ route('orders.status-check', $o) }}">
                                                @csrf
                                                <button
                                                    class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Cek
                                                    status</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="9">Belum ada data.</td>
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
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.js-copy-id').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.getAttribute('data-copy');
                        let ok = false;
                        if (navigator.clipboard && window.isSecureContext) {
                            try {
                                await navigator.clipboard.writeText(id);
                                ok = true;
                            } catch {}
                        }
                        if (!ok) {
                            const ta = document.createElement('textarea');
                            ta.value = id;
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
                        }, 1000);
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
