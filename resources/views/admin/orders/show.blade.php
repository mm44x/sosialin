<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Detail Order #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500">User</div>
                        <div class="font-medium">{{ $order->user->name ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ $order->user->email ?? '' }}</div>
                    </div>
                    <div>
                        @php $st = strtolower($order->status ?? ''); @endphp
                        <div class="text-slate-500">Status</div>
                        <span @class([
                            'inline-block mt-1 px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
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
                    </div>
                    <div>
                        <div class="text-slate-500">Layanan</div>
                        <div class="font-medium">{{ $order->service->public_name ?? ($order->service->name ?? '-') }}
                        </div>
                        <div class="text-xs text-slate-500">Kategori: {{ $order->service->category->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Provider Order ID</div>
                        <div class="font-medium">{{ $order->provider_order_id ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Quantity</div>
                        <div class="font-medium">{{ number_format($order->quantity) }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Biaya</div>
                        <div class="font-medium">Rp {{ number_format($order->cost, 2) }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <div class="text-slate-500">Link</div>
                        <div class="font-mono break-all">{{ $order->link }}</div>
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <a href="{{ url()->previous() ?: route('admin.orders.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>

                    @if (in_array($st, ['pending', 'processing', 'partial']) && $order->provider_order_id)
                        <form method="POST" action="{{ route('orders.status-check', $order) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                Cek status
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Timeline status (jika ada) --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <h3 class="font-semibold mb-3">Timeline Status</h3>
                @if (empty($timeline))
                    <p class="text-sm text-slate-500">Belum ada histori.</p>
                @else
                    <ul class="text-sm space-y-2">
                        @foreach ($timeline as $t)
                            <li class="flex items-start gap-3">
                                <span class="mt-1 inline-block w-2 h-2 rounded-full bg-primary"></span>
                                <div>
                                    <div class="font-medium">{{ $t['at'] ?? '-' }}</div>
                                    <div class="text-slate-600 dark:text-slate-300">
                                        Provider: <code>{{ $t['status'] ?? '-' }}</code>,
                                        Mapped: <code>{{ $t['mapped'] ?? '-' }}</code>,
                                        Remains: <code>{{ $t['remains'] ?? '-' }}</code>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
