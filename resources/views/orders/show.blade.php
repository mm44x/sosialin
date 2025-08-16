<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Detail Order #{{ $order->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Lihat detail lengkap dan timeline status order Anda
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('orders.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Riwayat Order
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Kartu ringkasan order --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
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
                        @php
                            $st = strtolower($order->status ?? '');
                            // Menentukan style berdasarkan status order
                            switch ($st) {
                                case 'pending':
                                    $badgeClasses =
                                        'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30';
                                    break;
                                case 'processing':
                                    $badgeClasses =
                                        'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30';
                                    break;
                                case 'completed':
                                    $badgeClasses =
                                        'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30';
                                    break;
                                case 'partial':
                                    $badgeClasses =
                                        'bg-orange-100 text-orange-800 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:ring-orange-400/30';
                                    break;
                                case 'canceled':
                                case 'cancelled':
                                    $badgeClasses =
                                        'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30';
                                    break;
                                case 'error':
                                    $badgeClasses =
                                        'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30';
                                    break;
                                default:
                                    $badgeClasses =
                                        'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30';
                            }
                        @endphp
                        <span
                            class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                            {{ ucfirst($st) ?: 'Unknown' }}
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

                    {{-- Link target --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <div class="text-slate-500 dark:text-slate-300">Link</div>
                        <div class="font-medium break-all">
                            <a href="{{ $order->link }}" target="_blank" class="underline hover:no-underline">
                                {{ $order->link }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Aksi bawah kartu detail --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                               border border-slate-200 dark:border-slate-700
                               hover:border-primary/20 dark:hover:border-primary/20 
                               hover:bg-primary/5 dark:hover:bg-primary/5
                               text-slate-700 dark:text-slate-300
                               transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali</span>
                    </a>

                    @unless (in_array($order->status, ['completed', 'error']))
                        <form method="POST" action="{{ route('orders.status-check', $order) }}" class="inline-flex">
                            @csrf
                            <button
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                                       bg-gradient-to-r from-primary to-purple-600 
                                       text-white font-medium shadow-sm 
                                       hover:shadow-md transition-all duration-300 hover:scale-105
                                       focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Cek status sekarang</span>
                            </button>
                        </form>
                    @endunless
                </div>
            </div>

            {{-- Timeline status --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div class="font-semibold">Timeline Status ({{ count($timeline) }})</div>

                    {{-- Toolbar filter --}}
                    <div class="flex items-center gap-2">
                        <select id="tlFilter"
                            class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                                   bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                   focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                   focus:border-primary/20 dark:focus:border-primary/20
                                   transition-colors">
                            <option value="">Semua status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="partial">Partial</option>
                            <option value="canceled">Canceled</option>
                            <option value="error">Error</option>
                        </select>
                    </div>
                </div>

                @if (count($timeline))
                    <div class="max-h-96 overflow-y-auto pe-2">
                        <ol id="tlList" class="ps-4 space-y-4">
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

                                <li class="js-tl-item" data-status="{{ $badge }}"
                                    data-text="{{ $textIndex }}">
                                    <div class="flex items-start gap-3 text-sm">
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

                    <div class="mt-4 text-center">
                        <button id="tlMore" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                                   border border-slate-200 dark:border-slate-700
                                   hover:border-primary/20 dark:hover:border-primary/20 
                                   hover:bg-primary/5 dark:hover:bg-primary/5
                                   text-slate-700 dark:text-slate-300
                                   transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                            <span>Tampilkan 20 lagi</span>
                        </button>
                    </div>
                @else
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        Belum ada data timeline. Gunakan tombol “Cek status sekarang”.
                    </div>
                @endif
            </div>

            @push('scripts')
                <script>
                    (() => {
                        const list = document.getElementById('tlList');
                        if (!list) return;

                        const items = Array.from(list.querySelectorAll('.js-tl-item'));
                        const btn = document.getElementById('tlMore');
                        const search = document.getElementById('tlSearch');
                        const filter = document.getElementById('tlFilter');

                        let showCount = Math.min(50, items.length); // tampil awal
                        const PAGE = 20;

                        function matchItem(el, q, st) {
                            const t = (el.dataset.text || '');
                            const s = (el.dataset.status || '');
                            return (!q || t.includes(q)) && (!st || s === st);
                        }

                        function totalMatched(q, st) {
                            return items.reduce((n, el) => n + (matchItem(el, q, st) ? 1 : 0), 0);
                        }

                        function applyFilter() {
                            const q = (search?.value || '').trim().toLowerCase();
                            const st = (filter?.value || '').trim().toLowerCase();

                            let shown = 0;
                            items.forEach((el) => {
                                const ok = matchItem(el, q, st);
                                if (ok && shown < showCount) {
                                    el.classList.remove('hidden');
                                    shown++;
                                } else {
                                    el.classList.add('hidden');
                                }
                            });

                            if (btn) {
                                const canMore = totalMatched(q, st);
                                btn.classList.toggle('hidden', showCount >= canMore);
                            }
                        }

                        // init
                        applyFilter();

                        btn?.addEventListener('click', () => {
                            showCount += PAGE;
                            applyFilter();
                        });

                        search?.addEventListener('input', () => {
                            showCount = Math.min(50, items.length);
                            applyFilter();
                        });

                        filter?.addEventListener('change', () => {
                            showCount = Math.min(50, items.length);
                            applyFilter();
                        });
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

                    if (window.toast) {
                        window.toast({
                            message: ok ? 'Disalin.' : 'Gagal menyalin',
                            type: ok ? 'success' : 'error',
                            timeout: 1500
                        });
                    }

                    const old = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = ok ?
                        '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                        '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                    setTimeout(() => {
                        btn.innerHTML = old;
                        btn.disabled = false;
                    }, 900);
                });
            })();
        </script>
    @endpush
</x-app-layout>
