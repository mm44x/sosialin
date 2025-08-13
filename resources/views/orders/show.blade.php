<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Detail Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex flex-wrap items-center gap-3 justify-between">
                    <div class="flex items-center gap-3">
                        <div class="text-lg font-semibold">Order #{{ $order->id }}</div>
                        <button type="button"
                            class="px-2 py-1 text-xs rounded-lg border dark:border-slate-600 hover:bg-primary/10 js-copy"
                            data-copy="{{ $order->id }}" aria-label="Salin Order ID">
                            Copy ID
                        </button>
                    </div>
                    <div>
                        <span @class([
                            'inline-block px-3 py-1 rounded-xl text-xs font-medium ring-1 ring-inset',
                            'bg-yellow-100 text-yellow-800 ring-yellow-200' => in_array(
                                $order->status,
                                ['pending', 'processing']),
                            'bg-green-100 text-green-800 ring-green-200' =>
                                $order->status === 'completed',
                            'bg-orange-100 text-orange-800 ring-orange-200' =>
                                $order->status === 'partial',
                            'bg-red-100 text-red-800 ring-red-200' => in_array($order->status, [
                                'canceled',
                                'error',
                            ]),
                            'bg-slate-100 text-slate-800 ring-slate-200' => !in_array($order->status, [
                                'pending',
                                'processing',
                                'completed',
                                'partial',
                                'canceled',
                                'error',
                            ]),
                        ])>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500 dark:text-slate-300">Layanan</div>
                        <div class="font-medium">{{ $order->service->public_name ?? $order->service->name }}</div>
                        <div class="text-xs text-slate-500">
                            Kategori: {{ $order->service->category->name ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-500 dark:text-slate-300">Qty</div>
                        <div class="font-medium">{{ number_format($order->quantity) }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500 dark:text-slate-300">Biaya</div>
                        <div class="font-medium">Rp {{ number_format($order->cost, 2) }}</div>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <div class="text-slate-500 dark:text-slate-300">Link</div>
                        <div class="font-medium break-all"><a href="{{ $order->link }}" target="_blank"
                                class="underline hover:no-underline">{{ $order->link }}</a></div>
                    </div>
                    <div>
                        <div class="text-slate-500 dark:text-slate-300">Provider Order ID</div>
                        <div class="flex items-center gap-2">
                            <div class="font-medium">{{ $order->provider_order_id ?? '—' }}</div>
                            @if ($order->provider_order_id)
                                <button type="button"
                                    class="px-2 py-1 text-xs rounded-lg border dark:border-slate-600 hover:bg-primary/10 js-copy"
                                    data-copy="{{ $order->provider_order_id }}" aria-label="Salin Provider Order ID">
                                    Copy
                                </button>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500">
                            Provider: {{ $order->service->provider->name ?? '—' }}
                        </div>
                    </div>
                </div>

                {{-- Aksi bawah kartu detail --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('orders.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        Kembali
                    </a>

                    @unless (in_array($order->status, ['completed', 'error']))
                        <form method="POST" action="{{ route('orders.status-check', $order) }}" class="inline-flex">
                            @csrf
                            <button
                                class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                Cek status sekarang
                            </button>
                        </form>
                    @endunless
                </div>

            </div>

            {{-- Timeline status --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="font-semibold mb-4">Timeline Status</div>

                @if (count($timeline))
                    <ol class="relative border-s border-slate-200 dark:border-white/10 ps-5 space-y-5">
                        @foreach ($timeline as $row)
                            <li class="relative">
                                <span
                                    class="absolute -start-2 top-1.5 h-3 w-3 rounded-full ring-2 ring-white dark:ring-slate-900
                                     @class([
                                         'bg-yellow-500' => in_array($row['mapped'] ?? '', [
                                             'pending',
                                             'processing',
                                         ]),
                                         'bg-green-600' => ($row['mapped'] ?? '') === 'completed',
                                         'bg-orange-500' => ($row['mapped'] ?? '') === 'partial',
                                         'bg-red-600' => in_array($row['mapped'] ?? '', ['canceled', 'error']),
                                         'bg-slate-400' => !in_array($row['mapped'] ?? '', [
                                             'pending',
                                             'processing',
                                             'completed',
                                             'partial',
                                             'canceled',
                                             'error',
                                         ]),
                                     ])"></span>
                                <div class="text-sm">
                                    <div class="font-medium">
                                        {{ ucfirst($row['mapped'] ?? ($row['status'] ?? 'unknown')) }}
                                        <span class="text-xs text-slate-500 ms-2">{{ $row['at'] ?? '' }}</span>
                                    </div>
                                    @if (isset($row['remains']))
                                        <div class="text-xs text-slate-600 dark:text-slate-300">Remains:
                                            {{ $row['remains'] }}</div>
                                    @endif
                                    @if (isset($row['source']))
                                        <div class="text-xs text-slate-400">Sumber: {{ $row['source'] }}</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <div class="text-sm text-slate-600 dark:text-slate-300">Belum ada data timeline. Gunakan tombol “Cek
                        status sekarang”.</div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                async function copyText(text) {
                    // Coba API modern
                    if (navigator.clipboard && window.isSecureContext) {
                        try {
                            await navigator.clipboard.writeText(text);
                            return true;
                        } catch {}
                    }
                    // Fallback
                    try {
                        const ta = document.createElement('textarea');
                        ta.value = text;
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.select();
                        const ok = document.execCommand('copy');
                        document.body.removeChild(ta);
                        return ok;
                    } catch {
                        return false;
                    }
                }

                // Event delegation: tangkap klik pada dokumen
                document.addEventListener('click', async (e) => {
                    const btn = e.target.closest('.js-copy');
                    if (!btn) return;
                    const val = btn.getAttribute('data-copy') || '';
                    const ok = await copyText(val);

                    // Umpan balik via toast (bila tersedia)
                    if (window.toast) {
                        window.toast({
                            message: ok ? 'Disalin.' : 'Gagal menyalin',
                            type: ok ? 'success' : 'error',
                            timeout: 1500
                        });
                    }

                    // Umpan balik visual pada tombol (opsional)
                    const old = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = ok ?
                        `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
         </svg>` :
                        `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
         </svg>`;
                    setTimeout(() => {
                        btn.innerHTML = old;
                        btn.disabled = false;
                    }, 900);
                });
            })();
        </script>
    @endpush

</x-app-layout>
