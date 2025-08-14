<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Buat Order — {{ $service->public_name ?? $service->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form id="order-form" method="POST" action="{{ route('orders.store', $service) }}"
                    data-rate-per-thousand="{{ $ratePerThousand }}" data-billing-min="{{ $billingMin }}"
                    data-min="{{ $service->min }}" data-max="{{ $service->max }}"
                    data-balance="{{ (float) (auth()->user()->wallet->balance ?? 0) }}"
                    onsubmit="const b=this.querySelector('[data-submit]'); if(b){ b.disabled=true; b.classList.add('opacity-60','cursor-not-allowed'); b.setAttribute('aria-busy','true'); }">
                    @csrf

                    {{-- Info layanan ringkas --}}
                    <div class="grid sm:grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <div class="text-slate-500 dark:text-slate-300">Layanan</div>
                            <div class="font-medium">{{ $service->public_name ?? $service->name }}</div>
                            <div class="text-xs text-slate-500">Kategori:
                                {{ $service->category->public_name ?? ($service->category->name ?? '—') }}</div>
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
                        <label class="block text-sm font-medium" for="link">Link / Target</label>
                        <input id="link" name="link" type="url" inputmode="url" value="{{ old('link') }}"
                            required placeholder="https://contoh.com/post/123..."
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            aria-describedby="linkHelp">
                        <p id="linkHelp" class="mt-1 text-xs text-slate-500">
                            Tautan harus diawali <code>http://</code> atau <code>https://</code>.
                        </p>
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium" for="qty">
                            Quantity
                            <span class="text-xs text-slate-500">
                                (min {{ number_format($service->min) }}, max {{ number_format($service->max) }})
                            </span>
                        </label>
                        <input id="qty" name="quantity" type="number" inputmode="numeric"
                            min="{{ $service->min }}" max="{{ $service->max }}"
                            value="{{ old('quantity', max($service->min, 100)) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required aria-describedby="qtyHelp qtyWarn">
                        <p id="qtyHelp" class="mt-1 text-xs text-slate-500">
                            Gunakan kelipatan yang relevan sesuai layanan.
                        </p>
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
                        <p id="warnFunds"
                            class="hidden mt-2 text-xs text-amber-700 bg-amber-100/70 dark:bg-amber-900/20 px-3 py-2 rounded-lg">
                            Saldo Anda kemungkinan kurang untuk jumlah ini.
                        </p>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex gap-3">
                        <a href="{{ route('services.index') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                            Kembali
                        </a>
                        <button type="submit" data-submit
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

                // Format Rupiah dengan fallback
                const fmtIDR = (n) => {
                    try {
                        return new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })
                            .format(Number(n));
                    } catch {
                        const num = Math.round(Number(n) * 100) / 100;
                        return num.toFixed(2);
                    }
                };
                const asRupiah = (n) => `Rp ${fmtIDR(n)}`;

                const perK = parseFloat(form.dataset.ratePerThousand || '0'); // harga/1000 (lokal) final
                const minCharge = parseFloat(form.dataset.billingMin || '0');
                const minQ = parseInt(form.dataset.min || '0', 10);
                const maxQ = parseInt(form.dataset.max || '0', 10);
                const balance = parseFloat(form.dataset.balance || '0'); // ← saldo mentah dari server

                const qtyEl = document.getElementById('qty');
                const estEl = document.getElementById('estCost');
                const warnEl = document.getElementById('qtyWarn');
                const fundsEl = document.getElementById('warnFunds');

                function recalc() {
                    let q = parseInt(qtyEl.value || '0', 10);
                    if (isNaN(q)) q = 0;

                    // Peringatan batas qty
                    if (q < minQ) {
                        warnEl.textContent = `Quantity di bawah minimal (${minQ}).`;
                        warnEl.classList.remove('hidden');
                    } else if (q > maxQ) {
                        warnEl.textContent = `Quantity melebihi maksimal (${maxQ}).`;
                        warnEl.classList.remove('hidden');
                    } else {
                        warnEl.textContent = '';
                        warnEl.classList.add('hidden');
                    }

                    // cost = max(minCharge, perK * (q/1000))
                    const raw = perK * (q / 1000);
                    const cost = Math.max(minCharge, Math.round(raw * 100) / 100);

                    estEl.textContent = asRupiah(cost);

                    // Info saldo (tidak memblokir submit)
                    if (balance > 0 && cost > balance) {
                        fundsEl.classList.remove('hidden');
                    } else {
                        fundsEl.classList.add('hidden');
                    }
                }

                qtyEl.addEventListener('input', recalc);
                qtyEl.addEventListener('change', recalc);
                recalc(); // initial
            })();
        </script>
    @endpush
</x-app-layout>
