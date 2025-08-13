<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Riwayat Order</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 text-green-800 ring-1 ring-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">Order ID</th>
                            <th class="py-2 px-4">Layanan</th>
                            <th class="py-2 px-4">Qty</th>
                            <th class="py-2 px-4">Biaya</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Provider ID</th>
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4 font-medium">
                                    #{{ $o->id }}
                                    <button type="button"
                                        class="ml-2 inline-flex items-center gap-1 px-2 py-1 rounded-lg border text-xs
           border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary js-copy-id"
                                        data-copy="{{ $o->id }}" aria-label="Salin Order ID {{ $o->id }}"
                                        title="Salin Order ID">
                                        Copy
                                    </button>
                                </td>

                                <td class="py-2 px-4">{{ $o->service->name ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $o->quantity }}</td>
                                <td class="py-2 px-4">Rp {{ number_format($o->cost, 2) }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs
                    @class([
                        'bg-yellow-100 text-yellow-800' =>
                            $o->status === 'pending' || $o->status === 'processing',
                        'bg-green-100 text-green-800' => $o->status === 'completed',
                        'bg-orange-100 text-orange-800' => $o->status === 'partial',
                        'bg-red-100 text-red-800' =>
                            $o->status === 'canceled' || $o->status === 'error',
                    ])">
                                        {{ ucfirst($o->status) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">{{ $o->provider_order_id ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $o->updated_at->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('orders.show', $o) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="8">Belum ada order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @push('scripts')
                    <script>
                        // Copy Order ID dengan umpan-balik singkat pada tombol
                        document.addEventListener('DOMContentLoaded', () => {
                            document.querySelectorAll('.js-copy-id').forEach(btn => {
                                btn.addEventListener('click', async () => {
                                    const id = btn.getAttribute('data-copy');
                                    let ok = false;

                                    // Coba API modern
                                    if (navigator.clipboard && window.isSecureContext) {
                                        try {
                                            await navigator.clipboard.writeText(id);
                                            ok = true;
                                        } catch (e) {
                                            ok = false;
                                        }
                                    }
                                    // Fallback
                                    if (!ok) {
                                        const ta = document.createElement('textarea');
                                        ta.value = id;
                                        ta.style.position = 'fixed';
                                        ta.style.opacity = '0';
                                        document.body.appendChild(ta);
                                        ta.select();
                                        try {
                                            ok = document.execCommand('copy');
                                        } catch (e) {
                                            ok = false;
                                        }
                                        document.body.removeChild(ta);
                                    }

                                    // Umpan-balik UI
                                    const old = btn.textContent.trim();
                                    btn.textContent = ok ? 'Disalin!' : 'Gagal';
                                    btn.disabled = true;
                                    setTimeout(() => {
                                        btn.textContent = old;
                                        btn.disabled = false;
                                    }, 1200);
                                });
                            });
                        });
                    </script>
                @endpush

            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
