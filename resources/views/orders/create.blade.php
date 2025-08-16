<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                Buat Order Baru
            </h2>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('services.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Layanan
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Service Info Card --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-tr from-primary to-purple-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ $service->public_name ?? $service->name }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $service->category->name ?? 'Uncategorized' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Order Form Card --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <form id="order-form" method="POST" action="{{ route('orders.store', $service) }}"
                    data-rate-per-thousand="{{ $ratePerThousand }}" data-billing-min="{{ $billingMin }}"
                    data-min="{{ $service->min }}" data-max="{{ $service->max }}"
                    data-balance="{{ (float) (auth()->user()->wallet->balance ?? 0) }}"
                    onsubmit="const b=this.querySelector('[data-submit]'); if(b){ b.disabled=true; b.classList.add('opacity-60','cursor-not-allowed'); b.setAttribute('aria-busy','true'); }"
                    class="space-y-6">
                    @csrf

                    {{-- Price Info --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        Rp {{ number_format($ratePerThousand, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-slate-500">Harga per 1000</div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        Rp {{ number_format($billingMin, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-slate-500">Minimal order</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Link Input --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="link">
                            Link / Target
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <input id="link" name="link" type="url" inputmode="url"
                                value="{{ old('link') }}" required placeholder="https://contoh.com/post/123..."
                                class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                aria-describedby="linkHelp">
                        </div>
                        <p id="linkHelp" class="text-xs text-slate-500">
                            Tautan harus diawali dengan <code
                                class="px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800">http://</code> atau
                            <code class="px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800">https://</code>
                        </p>
                    </div>

                    {{-- Quantity Input --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="qty">
                            Quantity
                        </label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <input id="qty" name="quantity" type="number" inputmode="numeric"
                                min="{{ $service->min }}" max="{{ $service->max }}"
                                value="{{ old('quantity', max($service->min, 100)) }}"
                                class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                required aria-describedby="qtyHelp qtyWarn">
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                Min: <span class="font-medium">{{ number_format($service->min) }}</span>,
                                Max: <span class="font-medium">{{ number_format($service->max) }}</span>
                            </div>
                        </div>
                        <p id="qtyWarn" class="hidden mt-2 text-sm text-red-600 dark:text-red-500"></p>
                    </div>

                    {{-- Cost Summary --}}
                    <div
                        class="p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-800/30
                                border border-slate-200/80 dark:border-slate-700/80">
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <div class="text-sm text-slate-500 dark:text-slate-400">Saldo Anda</div>
                                <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-sm text-slate-500 dark:text-slate-400">Total Biaya</div>
                                <div id="estCost"
                                    class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                                    Rp 0
                                </div>
                            </div>
                        </div>

                        <div id="warnFunds"
                            class="hidden mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="text-sm text-amber-800 dark:text-amber-200">
                                    Saldo Anda kemungkinan tidak mencukupi untuk order ini.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3">
                        <button type="submit" data-submit
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl 
                                   bg-gradient-to-r from-primary to-purple-600 
                                   text-white font-medium shadow-sm 
                                   hover:shadow-md transition-all duration-300 hover:scale-105
                                   focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:opacity-60 disabled:cursor-not-allowed
                                   disabled:hover:scale-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Buat Order</span>
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
