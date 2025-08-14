<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Admin — Detail Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Kartu detail utama --}}
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
                        @php $st = strtolower($order->status ?? ''); @endphp
                        <span @class([
                            'inline-block px-3 py-1 rounded-xl text-xs font-medium ring-1 ring-inset',
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
                        ])>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500 dark:text-slate-300">User</div>
                        <div class="font-medium">{{ $order->user->name ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ $order->user->email ?? '' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500 dark:text-slate-300">Layanan</div>
                        <div class="font-medium">{{ $order->service->public_name ?? ($order->service->name ?? '-') }}
                        </div>
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
                        <div class="font-medium break-all">
                            <a href="{{ $order->link }}" target="_blank" class="underline hover:no-underline">
                                {{ $order->link }}
                            </a>
                        </div>
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
                    <a href="{{ route('admin.orders.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                        Kembali
                    </a>

                    @unless (in_array($order->status, ['completed', 'error']))
                        <form method="POST" action="{{ route('admin.orders.status-check', $order) }}" class="inline-flex">
                            @csrf
                            <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">
                                Cek status sekarang
                            </button>
                        </form>
                    @endunless
                </div>
            </div>

            {{-- Timeline status --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="font-semibold">Timeline Status ({{ count($timeline) }})</div>

                    @if (count($timeline))
                        <div class="flex items-center gap-2">
                            <input id="tlSearch" type="text" placeholder="Cari status/teks..."
                                class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600 text-sm">
                            <select id="tlFilter"
                                class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600 text-sm">
                                <option value="">Semua status</option>
                                @foreach (['pending', 'processing', 'completed', 'partial', 'canceled', 'error'] as $opt)
                                    <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                @if (count($timeline))
                    <div id="tlWrap" class="max-h-96 overflow-y-auto pe-2">
                        <ol id="tlList"
                            class="relative border-s border-slate-200 dark:border-white/10 ps-4 space-y-4">
                            @foreach ($timeline as $row)
                                @php
                                    $mapped = strtolower($row['mapped'] ?? '');
                                    $status = strtolower($row['status'] ?? '');
                                    $badge = $mapped ?: $status;
                                    $dotCls = in_array($badge, ['pending', 'processing'])
                                        ? 'bg-yellow-500'
                                        : ($badge === 'completed'
                                            ? 'bg-green-600'
                                            : ($badge === 'partial'
                                                ? 'bg-orange-500'
                                                : (in_array($badge, ['canceled', 'cancelled', 'error'])
                                                    ? 'bg-red-600'
                                                    : 'bg-slate-400')));
                                    $textIndex = strtolower(
                                        trim(
                                            ($row['mapped'] ?? '') .
                                                ' ' .
                                                ($row['status'] ?? '') .
                                                ' ' .
                                                (string) ($row['remains'] ?? '') .
                                                ' ' .
                                                ($row['source'] ?? '') .
                                                ' ' .
                                                ($row['at'] ?? ''),
                                        ),
                                    );
                                @endphp

                                {{-- FLEX layout (tidak pakai absolute) --}}
                                <li class="js-tl-item" data-status="{{ $badge }}"
                                    data-text="{{ $textIndex }}">
                                    <div class="flex items-start gap-3">
                                        <span
                                            class="mt-1 h-3 w-3 rounded-full ring-2 ring-white dark:ring-slate-900 {{ $dotCls }} flex-none"></span>

                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium leading-5 truncate">
                                                {{ ucfirst($row['mapped'] ?? ($row['status'] ?? 'unknown')) }}
                                            </div>
                                            @if (isset($row['remains']))
                                                <div class="text-xs text-slate-600 dark:text-slate-300">Remains:
                                                    {{ $row['remains'] }}</div>
                                            @endif
                                            @if (isset($row['source']))
                                                <div class="text-xs text-slate-400">Sumber: {{ $row['source'] }}</div>
                                            @endif
                                        </div>

                                        <div class="shrink-0 text-xs text-slate-500">
                                            {{ $row['at'] ?? '' }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>


                    </div>

                    @if (count($timeline) > 20)
                        <div class="mt-3 text-center">
                            <button id="tlShowMore"
                                class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                Tampilkan 20 lagi
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        Belum ada data timeline. Gunakan tombol “Cek status sekarang”.
                    </div>
                @endif
            </div>

            @push('scripts')
                <script>
                    (() => {
                        const ITEMS_PER_BATCH = 20;
                        const list = document.getElementById('tlList');
                        if (!list) return;

                        const items = Array.from(list.querySelectorAll('.js-tl-item'));
                        const btn = document.getElementById('tlShowMore');
                        const qEl = document.getElementById('tlSearch');
                        const stEl = document.getElementById('tlFilter');

                        let shown = 0;

                        function showNextBatch() {
                            const next = Math.min(items.length, shown + ITEMS_PER_BATCH);
                            for (let i = shown; i < next; i++) items[i].classList.remove('hidden');
                            shown = next;
                            if (btn) btn.classList.toggle('hidden', shown >= items.length);
                        }

                        function applyFilter() {
                            const q = (qEl?.value || '').toLowerCase().trim();
                            const st = (stEl?.value || '').toLowerCase().trim();

                            // Bila filter aktif, tampilkan semua yg match & sembunyikan tombol "tampilkan lagi"
                            let matchCount = 0;
                            items.forEach((el, idx) => {
                                const text = (el.dataset.text || '');
                                const status = (el.dataset.status || '');
                                const match = (q === '' || text.includes(q)) && (st === '' || status === st);
                                el.style.display = match ? '' : 'none';
                                // jika filter kosong, kembali ke mode batching (display default)
                                if (q === '' && st === '') el.classList.toggle('hidden', idx >= shown);
                                matchCount += match ? 1 : 0;
                            });

                            if (btn) {
                                if (q !== '' || st !== '') btn.classList.add('hidden');
                                else btn.classList.toggle('hidden', shown >= items.length);
                            }
                        }

                        // Init: sembunyikan semua, tampilkan batch awal
                        items.forEach(el => el.classList.add('hidden'));
                        showNextBatch();

                        btn?.addEventListener('click', showNextBatch);
                        qEl?.addEventListener('input', applyFilter);
                        stEl?.addEventListener('change', applyFilter);
                    })();
                </script>
            @endpush


        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                async function copyText(text) {
                    if (navigator.clipboard && window.isSecureContext) {
                        try {
                            await navigator.clipboard.writeText(text);
                            return true;
                        } catch {}
                    }
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

                document.addEventListener('click', async (e) => {
                    const btn = e.target.closest('.js-copy');
                    if (!btn) return;
                    const val = btn.getAttribute('data-copy') || '';
                    const ok = await copyText(val);

                    // Toast (opsional)
                    if (window.toast) {
                        window.toast({
                            message: ok ? 'Disalin.' : 'Gagal menyalin',
                            type: ok ? 'success' : 'error',
                            timeout: 1500
                        });
                    }

                    // Feedback visual pada tombol
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
