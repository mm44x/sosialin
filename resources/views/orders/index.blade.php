{{-- Path : resources/views/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Riwayat Order</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Toolbar filter --}}
            <form method="GET" action="{{ route('orders.index') }}" class="mb-4 grid md:grid-cols-4 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Cari (ID/Link/Layanan)</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? request('search') }}"
                        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                        placeholder="mis. #123, instagram.com/..., atau nama layanan">
                </div>

                <div>
                    <label class="block text-sm font-medium">Status</label>
                    @php $st = strtolower($filters['status'] ?? request('status')); @endphp
                    <select name="status"
                        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                        <option value="">Semua</option>
                        @foreach (['pending', 'processing', 'completed', 'partial', 'canceled', 'error'] as $opt)
                            <option value="{{ $opt }}" @selected($st === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                    <a href="{{ route('orders.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
                </div>
            </form>


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
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4 font-medium">
                                    <div class="flex items-center gap-2">
                                        <span>#{{ $o->id }}</span>
                                        <button type="button"
                                            class="p-1 rounded-lg hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary js-copy-id"
                                            data-copy="{{ $o->id }}"
                                            aria-label="Salin Order ID {{ $o->id }}" title="Salin Order ID">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <td class="py-2 px-4">{{ $o->service->name ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $o->quantity }}</td>
                                <td class="py-2 px-4">Rp {{ number_format($o->cost, 2) }}</td>
                                @php $st = strtolower($o->status ?? ''); @endphp
                                <td class="py-2 px-4">
                                    <span @class([
                                        // kelas dasar badge
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        // varian warna per status
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
                                        // fallback bila tak cocok (agar tetap terlihat)
                                        'bg-slate-100 text-slate-800 ring-slate-200' => !in_array($st, [
                                            'pending',
                                            'processing',
                                            'completed',
                                            'partial',
                                            'canceled',
                                            'cancelled',
                                            'error',
                                        ]),
                                    ])>
                                        {{ ucfirst($st) ?: 'Unknown' }}
                                    </span>
                                </td>
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
                                    const old = btn.innerHTML;
                                    btn.disabled = true;
                                    if (ok) {
                                        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
</svg>`;
                                    } else {
                                        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
</svg>`;
                                    }

                                    setTimeout(() => {
                                        btn.innerHTML = old;
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
