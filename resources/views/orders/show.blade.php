<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Detail Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-green-50 text-green-800 ring-1 ring-green-200">{{ session('status') }}
                </div>
            @endif

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Layanan</dt>
                        <dd class="font-medium">{{ $order->service->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Kategori</dt>
                        <dd class="font-medium">{{ $order->service->category->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Qty</dt>
                        <dd class="font-medium">{{ $order->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Biaya</dt>
                        <dd class="font-medium">Rp {{ number_format($order->cost, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Status</dt>
                        <dd class="font-semibold">{{ ucfirst($order->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Provider Order ID</dt>
                        <dd class="font-medium">{{ $order->provider_order_id ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slateText dark:text-slate-300">Link</dt>
                        <dd class="font-medium break-all">{{ $order->link }}</dd>
                    </div>
                </dl>

                <div class="mt-4 flex gap-3">
                    <a href="{{ route('orders.index') }}"
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10">Kembali</a>

                    @if ($order->provider_order_id)
                        <form method="POST" action="{{ route('orders.refresh', $order) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">
                                Cek Status Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @php $last = $order->meta['last_status_response'] ?? null; @endphp
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <h3 class="font-semibold mb-2">Respons Terakhir Provider</h3>
                @if ($last)
                    <pre class="text-xs overflow-x-auto">{{ json_encode($last, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @else
                    <p class="text-sm text-slateText dark:text-slate-300">Belum ada respons yang tersimpan.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
