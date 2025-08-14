<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">
            Admin — User #{{ $user->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Ringkasan user --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div class="p-4 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                        <div class="text-slate-500 dark:text-slate-300">Nama</div>
                        <div class="font-medium">{{ $user->name }}</div>
                    </div>
                    <div
                        class="p-4 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 break-all">
                        <div class="text-slate-500 dark:text-slate-300">Email</div>
                        <div class="font-medium">{{ $user->email }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                        <div class="text-slate-500 dark:text-slate-300">Saldo</div>
                        <div class="font-bold">Rp {{ number_format($balance, 2) }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                        <div class="text-slate-500 dark:text-slate-300">Total Orders</div>
                        <div class="font-medium">{{ (int) ($user->orders_count ?? 0) }}</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                        ← Kembali ke daftar
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                        Edit User
                    </a>
                    <a href="{{ route('admin.orders.index', ['q' => $user->email]) }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                        Lihat semua order user ini
                    </a>
                </div>
            </div>

            {{-- Order terbaru --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Order Terbaru</h3>
                    <a href="{{ route('admin.orders.index', ['q' => $user->email]) }}"
                        class="text-sm underline hover:no-underline">Lihat semua</a>
                </div>

                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left">
                            <tr>
                                <th class="py-2 px-4">#</th>
                                <th class="py-2 px-4">Layanan</th>
                                <th class="py-2 px-4">Qty</th>
                                <th class="py-2 px-4">Biaya</th>
                                <th class="py-2 px-4">Status</th>
                                <th class="py-2 px-4">Updated</th>
                                <th class="py-2 px-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $o)
                                @php $st = strtolower($o->status ?? ''); @endphp
                                <tr class="border-t border-slate-200/60 dark:border-white/10">
                                    <td class="py-2 px-4">
                                        <div class="flex items-center gap-2">
                                            <span>#{{ $o->id }}</span>
                                            <button type="button"
                                                class="p-1 rounded-lg hover:bg-primary/10 focus:ring-2 focus:ring-primary js-copy"
                                                data-copy="{{ $o->id }}" title="Salin Order ID">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4">
                                        <div class="font-medium">
                                            {{ $o->service->public_name ?? ($o->service->name ?? '-') }}
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $o->service->category->name ?? '—' }}
                                        </div>
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
                                    <td class="py-2 px-4">{{ $o->updated_at?->diffForHumans() }}</td>
                                    <td class="py-2 px-4">
                                        <a href="{{ route('admin.orders.show', $o) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-3 px-4" colspan="7">Belum ada order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Transaksi Wallet terbaru --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <h3 class="font-semibold">Transaksi Wallet Terbaru</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left">
                            <tr>
                                <th class="py-2 px-4">#</th>
                                <th class="py-2 px-4">Tipe</th>
                                <th class="py-2 px-4">Nominal</th>
                                <th class="py-2 px-4">Keterangan</th>
                                <th class="py-2 px-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTx as $t)
                                @php
                                    $type = strtolower($t->type ?? '');
                                    $isCredit = $type === 'credit';
                                    $amount = (float) ($t->amount ?? 0);
                                @endphp
                                <tr class="border-t border-slate-200/60 dark:border-white/10">
                                    <td class="py-2 px-4">{{ $t->id }}</td>
                                    <td class="py-2 px-4">
                                        <span
                                            class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset
                                        {{ $isCredit ? 'bg-green-100 text-green-800 ring-green-200' : 'bg-red-100 text-red-800 ring-red-200' }}">
                                            {{ ucfirst($type) ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 {{ $isCredit ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $isCredit ? '+' : '-' }} Rp {{ number_format(abs($amount), 2) }}
                                    </td>
                                    <td class="py-2 px-4">{{ $t->description ?? '—' }}</td>
                                    <td class="py-2 px-4">{{ $t->created_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-3 px-4" colspan="5">Belum ada transaksi.</td>
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
