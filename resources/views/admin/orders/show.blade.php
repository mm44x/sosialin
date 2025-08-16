<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Detail Order #{{ $order->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Lihat detail lengkap dan timeline status order
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('admin.orders.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Daftar Orders
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Order Details Card --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex flex-wrap items-center gap-3 justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="text-xl font-bold text-slate-900 dark:text-white">Order #{{ $order->id }}</div>
                        <button type="button"
                            class="px-3 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 
                                   hover:border-primary/20 dark:hover:border-primary/20 
                                   hover:bg-primary/5 dark:hover:bg-primary/5
                                   text-slate-600 dark:text-slate-400
                                   js-copy transition-all duration-300"
                            data-copy="{{ $order->id }}" aria-label="Salin Order ID">
                            Copy ID
                        </button>
                    </div>
                    <div>
                        @php $st = strtolower($order->status ?? ''); @endphp
                        @php
                            $statusBadge = match($st) {
                                'pending', 'processing' => 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30',
                                'completed' => 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30',
                                'partial' => 'bg-orange-100 text-orange-800 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:ring-orange-400/30',
                                'canceled', 'cancelled', 'error' => 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30',
                                default => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                            };
                        @endphp
                        <span class="inline-block px-4 py-2 rounded-xl text-sm font-medium ring-1 ring-inset {{ $statusBadge }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">User</div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $order->user->name ?? '—' }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->user->email ?? '' }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Layanan</div>
                        <div class="font-semibold text-slate-900 dark:text-white">
                            {{ $order->service->public_name ?? ($order->service->name ?? '-') }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Kategori: {{ $order->service->category->name ?? '—' }}
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Qty</div>
                        <div class="font-semibold text-lg text-slate-900 dark:text-white">{{ number_format($order->quantity) }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Biaya</div>
                        <div class="font-semibold text-lg text-slate-900 dark:text-white">Rp {{ number_format($order->cost, 2) }}</div>
                    </div>

                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Provider Order ID</div>
                        <div class="flex items-center gap-2">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $order->provider_order_id ?? '—' }}</div>
                            @if ($order->provider_order_id)
                                <button type="button"
                                    class="px-2 py-1 text-xs rounded-lg border border-slate-200 dark:border-slate-700 
                                           hover:border-primary/20 dark:hover:border-primary/20 
                                           hover:bg-primary/5 dark:hover:bg-primary/5
                                           text-slate-600 dark:text-slate-400
                                           js-copy transition-all duration-300"
                                    data-copy="{{ $order->provider_order_id }}" aria-label="Salin Provider Order ID">
                                    Copy
                                </button>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Provider: {{ $order->service->provider->name ?? '—' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3 p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Link</div>
                        <div class="font-semibold text-slate-900 dark:text-white break-all">
                            <a href="{{ $order->link }}" target="_blank" 
                               class="text-primary hover:text-primary/80 transition-colors underline hover:no-underline">
                                {{ $order->link }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
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
                        <form method="POST" action="{{ route('admin.orders.status-check', $order) }}" class="inline-flex">
                            @csrf
                            <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
                                           bg-gradient-to-r from-primary to-purple-600 
                                           text-white font-medium shadow-sm 
                                           hover:shadow-md transition-all duration-300 hover:scale-105
                                           focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Cek Status Sekarang</span>
                            </button>
                        </form>
                    @endunless
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Timeline Status</h3>
                            <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                                {{ count($timeline) }} entries
                            </span>
                        </div>

                        @if (count($timeline))
                            <div class="flex items-center gap-2">
                                <select id="tlFilter"
                                    class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors text-sm">
                                    <option value="">Semua status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="partial">Partial</option>
                                    <option value="canceled">Canceled</option>
                                    <option value="error">Error</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

                @if (count($timeline))
                    <div class="max-h-96 overflow-y-auto p-6">
                        <ol id="tlList"
                            class="relative border-l-2 border-slate-200 dark:border-slate-700 ml-12 pl-4">
                            @foreach ($timeline as $index => $row)
                                @php
                                    $mapped = strtolower($row['mapped'] ?? '');
                                    $status = strtolower($row['status'] ?? '');
                                    $badge = $mapped ?: $status;
                                    $isLatest = $index === 0;

                                    // Menentukan warna dot berdasarkan status
                                    $dotClasses = '';
                                    $dotIcon = '';

                                    if (in_array($badge, ['pending', 'processing'])) {
                                        $dotClasses = 'bg-yellow-100 dark:bg-yellow-900';
                                        $dotIcon = '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                                    } elseif ($badge === 'completed') {
                                        $dotClasses = 'bg-green-100 dark:bg-green-900';
                                        $dotIcon = '<path d="M5 13l4 4L19 7"/>';
                                    } elseif ($badge === 'partial') {
                                        $dotClasses = 'bg-orange-100 dark:bg-orange-900';
                                        $dotIcon = '<path d="M7 16V4M7 4L3 8m4-4l4 4"/>';
                                    } elseif (in_array($badge, ['canceled', 'cancelled', 'error'])) {
                                        $dotClasses = 'bg-red-100 dark:bg-red-900';
                                        $dotIcon = '<path d="M6 18L18 6M6 6l12 12"/>';
                                    } else {
                                        $dotClasses = 'bg-slate-100 dark:bg-slate-900';
                                        $dotIcon =
                                            '<path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                                    }

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

                                <li class="js-tl-item relative pb-6 last:pb-0" data-status="{{ $badge }}"
                                    data-text="{{ $textIndex }}">
                                    <span
                                        class="absolute flex items-center justify-center w-6 h-6 {{ $dotClasses }} rounded-full -left-14 ring-4 ring-white dark:ring-slate-900">
                                        <svg class="w-3 h-3 text-slate-800 dark:text-slate-300" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            {!! $dotIcon !!}
                                        </svg>
                                    </span>

                                    <div class="ml-2">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                                {{ ucfirst($row['mapped'] ?? ($row['status'] ?? 'unknown')) }}
                                            </h3>
                                            @if ($isLatest)
                                                <span
                                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                                    Latest
                                                </span>
                                            @endif
                                        </div>

                                        <time
                                            class="block mb-3 text-sm font-normal leading-none text-slate-500 dark:text-slate-400">
                                            {{ $row['at'] ?? 'Waktu tidak tersedia' }}
                                        </time>

                                        <div class="space-y-2">
                                            @if (isset($row['remains']))
                                                <div
                                                    class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    <span>Remains: <span
                                                            class="font-medium">{{ $row['remains'] }}</span></span>
                                                </div>
                                            @endif
                                            @if (isset($row['source']))
                                                <div
                                                    class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                    <span>Sumber: <span
                                                            class="font-medium">{{ $row['source'] }}</span></span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    @if (count($timeline) > 20)
                        <div class="mt-4 text-center p-4">
                            <button id="tlShowMore"
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
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada timeline</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Gunakan tombol "Cek Status Sekarang" untuk melihat timeline status order.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const list = document.getElementById('tlList');
                if (!list) return;

                const items = Array.from(list.querySelectorAll('.js-tl-item'));
                const btn = document.getElementById('tlShowMore');
                const filter = document.getElementById('tlFilter');

                let showCount = Math.min(50, items.length); // tampil awal
                const PAGE = 20;

                function matchItem(el, st) {
                    const s = (el.dataset.status || '');
                    return (!st || s === st);
                }

                function totalMatched(st) {
                    return items.reduce((n, el) => n + (matchItem(el, st) ? 1 : 0), 0);
                }

                function applyFilter() {
                    const st = (filter?.value || '').trim().toLowerCase();

                    let shown = 0;
                    items.forEach((el) => {
                        const ok = matchItem(el, st);
                        if (ok && shown < showCount) {
                            el.classList.remove('hidden');
                            shown++;
                        } else {
                            el.classList.add('hidden');
                        }
                    });

                    if (btn) {
                        const canMore = totalMatched(st);
                        btn.classList.toggle('hidden', showCount >= canMore);
                    }
                }

                // init
                applyFilter();

                btn?.addEventListener('click', () => {
                    showCount += PAGE;
                    applyFilter();
                });

                filter?.addEventListener('change', () => {
                    showCount = Math.min(50, items.length);
                    applyFilter();
                });
            })();
        </script>
    @endpush

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
                        `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                           </svg>` :
                        `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
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
