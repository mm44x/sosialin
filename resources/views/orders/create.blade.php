<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Buat Order — {{ $service->public_name ?? $service->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-2xl bg-red-50 text-red-800 ring-1 ring-red-200">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('orders.store', $service) }}" id="order-form"
                    data-rate-per-thousand="{{ $ratePerThousand }}" data-billing-min="{{ $billingMin }}"
                    data-min="{{ $service->min }}" data-max="{{ $service->max }}">
                    @csrf

                    {{-- Info layanan ringkas --}}
                    <div class="grid sm:grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <div class="text-slate-500 dark:text-slate-300">Layanan</div>
                            <div class="font-medium">{{ $service->public_name ?? $service->name }}</div>
                            <div class="text-xs text-slate-500">Kategori: {{ $service->category->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-slate-500 dark:text-slate-300">Harga (per 1000)</div>
                            <div class="font-medium">Rp {{ number_format($ratePerThousand, 2) }}</div>
                            <div class="text-xs text-slate-500">Minimal tagihan: Rp {{ number_format($billingMin, 2) }}
                            </div>
                        </div>
                    </div>

                    {{-- Link --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Link / Target</label>
                        <input name="link" value="{{ old('link') }}" required
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            placeholder="Tempelkan link target di sini">
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium">
                            Quantity <span class="text-xs text-slate-500">(min {{ number_format($service->min) }}, max
                                {{ number_format($service->max) }})</span>
                        </label>
                        <input type="number" name="quantity" id="qty" inputmode="numeric"
                            min="{{ $service->min }}" max="{{ $service->max }}"
                            value="{{ old('quantity', max($service->min, 100)) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        <p id="qtyHelp" class="mt-1 text-xs text-slate-500">Gunakan kelipatan yang relevan sesuai
                            layanan.</p>
                        <p id="qtyWarn" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>

                    {{-- Ringkasan biaya realtime --}}
                    <div
                        class="mb-4 p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="text-sm">
                                <div>Per 1000: <span class="font-medium">Rp
                                        {{ number_format($ratePerThousand, 2) }}</span></div>
                                <div class="text-slate-500">Min charge: Rp {{ number_format($billingMin, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-slate-500">Perkiraan biaya</div>
                                <div id="estCost" class="text-xl font-bold">Rp 0,00</div>
                            </div>
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex gap-3">
                        <a href="{{ route('services.index') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                            Kembali
                        </a>
                        <button
                            class="px-4 py-2 rounded-2xl bg-primary text-white hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                            Buat Order
                        </button>
                    </div>
                </form>
            </div>

            {{-- Catatan transparansi publik (tanpa bocorkan markup/provider) --}}
            <p class="mt-4 text-xs text-slate-500 dark:text-slate-300">
                * Biaya dihitung otomatis berdasarkan quantity dan kebijakan penagihan layanan.
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const form = document.getElementById('order-form');
                if (!form) return;

                const fmt = (n) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(n).replace('IDR', 'Rp');
                const perK = parseFloat(form.dataset.ratePerThousand || '0');
                const minCharge = parseFloat(form.dataset.billingMin || '0');
                const min = parseInt(form.dataset.min || '0', 10);
                const max = parseInt(form.dataset.max || '0', 10);

                const qtyEl = document.getElementById('qty');
                const estEl = document.getElementById('estCost');
                const warnEl = document.getElementById('qtyWarn');

                function recalc() {
                    const q = parseInt(qtyEl.value || '0', 10);
                    let warn = '';
                    if (q < min) warn = `Quantity di bawah minimal (${min}).`;
                    else if (q > max) warn = `Quantity melebihi maksimal (${max}).`;

                    if (warn) {
                        warnEl.textContent = warn;
                        warnEl.classList.remove('hidden');
                    } else {
                        warnEl.classList.add('hidden');
                        warnEl.textContent = '';
                    }

                    // cost = max(minCharge, perK * (q/1000))
                    const raw = perK * (q / 1000);
                    const cost = Math.max(minCharge, Math.round(raw * 100) / 100);
                    estEl.textContent = fmt(cost);
                }

                qtyEl.addEventListener('input', recalc);
                qtyEl.addEventListener('change', recalc);
                recalc(); // initial
            })();
        </script>
    @endpush
</x-app-layout>
