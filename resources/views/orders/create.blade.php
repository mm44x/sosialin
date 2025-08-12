<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Buat Pesanan — {{ $service->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        @if (session('status'))
            <div class="mb-4 p-4 rounded-2xl bg-yellow-50 text-yellow-800 ring-1 ring-yellow-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 p-4 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="text-sm text-slateText dark:text-slate-300">Saldo Anda</div>
                <div class="text-2xl font-bold">
                    Rp {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                </div>
            </div>
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-50 text-red-800 ring-1 ring-red-200">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">

                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Rate / 1000 (USD, base)</dt>
                        <dd class="font-medium">$ {{ number_format($baseRateUSD, 4) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Markup Provider</dt>
                        <dd class="font-medium">{{ $markup }}%</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slateText dark:text-slate-300">Rate / 1000 (efektif)</dt>
                        <dd class="font-semibold">
                            Rp <span id="rate1000">{{ number_format($ratePerThousand, 2) }}</span>
                            <span class="text-xs text-slateText dark:text-slate-400">(multiplier:
                                {{ $mult }})</span>
                        </dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('orders.store', $service) }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="link" class="block text-sm font-medium">Link / Username Target</label>
                        <input id="link" name="link" value="{{ old('link') }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            placeholder="https://instagram.com/..." required>
                        @error('link')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium">
                            Jumlah (min {{ $service->min }}, max {{ $service->max }})
                        </label>
                        <input id="quantity" name="quantity" type="number" min="{{ $service->min }}"
                            max="{{ $service->max }}" value="{{ old('quantity', $service->min) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('quantity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-sm">
                        <div class="text-slateText dark:text-slate-300">Estimasi Biaya</div>
                        <div class="text-2xl font-bold">Rp <span id="estCost">0.00</span></div>
                        <p class="mt-1 text-slateText dark:text-slate-400">Perhitungan final dilakukan di server saat
                            submit.</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('services.index') }}"
                            class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary">
                            Kembali
                        </a>
                        <button
                            class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary">
                            Buat Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const qtyEl = document.getElementById('quantity');
                const estEl = document.getElementById('estCost');

                const ratePerThousand = parseFloat({{ json_encode($ratePerThousand) }});
                const minCharge = parseFloat({{ json_encode($billingMin) }});

                function recalc() {
                    const q = parseFloat(qtyEl.value || 0);
                    let cost = ratePerThousand * (q / 1000);
                    cost = Math.round(cost * 100) / 100; // 2 desimal
                    if (cost < minCharge) cost = minCharge;
                    estEl.textContent = cost.toFixed(2);
                }

                qtyEl.addEventListener('input', recalc);
                recalc();
            });
        </script>
    @endpush


</x-app-layout>
