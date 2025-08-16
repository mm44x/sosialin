<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — API Log #{{ $log->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Detail lengkap log API dari provider
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('admin.api-logs.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Daftar API Logs
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Log Info Card --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Informasi Log</h3>
                </div>

                <div class="grid md:grid-cols-2 gap-6 text-sm">
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Provider</div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $log->provider->name ?? '—' }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Endpoint</div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $log->endpoint }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Status Code</div>
                        <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset
                               {{ (int) $log->status_code >= 200 && (int) $log->status_code < 400
                                   ? 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30'
                                   : 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30' }}">
                            {{ $log->status_code }}
                        </span>
                    </div>
                    <div class="p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Durasi</div>
                        <div class="font-semibold text-slate-900 dark:text-white">{{ number_format((int) $log->duration_ms) }} ms</div>
                    </div>
                    <div class="md:col-span-2 p-4 rounded-xl bg-white/50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60">
                        <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Waktu</div>
                        <div class="font-semibold text-slate-900 dark:text-white">
                            {{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '—' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request Data --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Request Data</h3>
                        </div>
                        <button type="button"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
                                   border border-slate-200 dark:border-slate-700
                                   hover:border-primary/20 dark:hover:border-primary/20 
                                   hover:bg-primary/5 dark:hover:bg-primary/5
                                   text-slate-700 dark:text-slate-300
                                   transition-all duration-300"
                            data-copy-target="#req-json">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Copy</span>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <pre id="req-json"
                        class="overflow-auto text-xs p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60 text-slate-700 dark:text-slate-300">
{{ json_encode($reqData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                    </pre>
                </div>
            </div>

            {{-- Response Data --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Response Data</h3>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" data-tab="pretty"
                                class="tab-btn inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
                                       border border-slate-200 dark:border-slate-700
                                       hover:border-primary/20 dark:hover:border-primary/20 
                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                       text-slate-700 dark:text-slate-300
                                       transition-all duration-300">Pretty</button>
                            <button type="button" data-tab="raw"
                                class="tab-btn inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
                                       border border-slate-200 dark:border-slate-700
                                       hover:border-primary/20 dark:hover:border-primary/20 
                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                       text-slate-700 dark:text-slate-300
                                       transition-all duration-300">Raw</button>
                            <button type="button"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
                                       border border-slate-200 dark:border-slate-700
                                       hover:border-primary/20 dark:hover:border-primary/20 
                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                       text-slate-700 dark:text-slate-300
                                       transition-all duration-300"
                                data-copy-target="#resp-active">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Copy</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Pretty JSON --}}
                    <pre id="resp-pretty"
                        class="resp-tab overflow-auto text-xs p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60 text-slate-700 dark:text-slate-300"
                        style="display: {{ $respIsJson ? 'block' : 'none' }}">
@if ($respIsJson)
{{ json_encode($respArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
@else
{{-- not json --}}
@endif
                    </pre>

                    {{-- Raw Response --}}
                    <div id="resp-raw" class="resp-tab" style="display: {{ $respIsJson ? 'none' : 'block' }}">
                        <pre id="resp-active"
                            class="overflow-auto text-xs p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200/60 dark:ring-slate-600/60 text-slate-700 dark:text-slate-300">{{ $respPreview }}</pre>
                        @if ($respTruncated)
                            <button type="button" id="btn-expand"
                                class="mt-3 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
                                       border border-slate-200 dark:border-slate-700
                                       hover:border-primary/20 dark:hover:border-primary/20 
                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                       text-slate-700 dark:text-slate-300
                                       transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <span>Tampilkan seluruh response</span>
                            </button>
                            <textarea id="resp-full"
                                class="hidden w-full mt-3 text-xs p-4 rounded-xl border border-slate-200 dark:border-slate-700 
                                       bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                rows="12">{{ $respRaw }}</textarea>
                        @endif
                    </div>

                    {{-- Hidden alias untuk Copy: saat tab Pretty aktif, targetkan ini --}}
                    <textarea id="resp-active" class="hidden">{{ $respIsJson ? json_encode($respArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $respRaw }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.api-logs.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
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
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Tab switching
                const tabs = document.querySelectorAll('.tab-btn');
                const pretty = document.getElementById('resp-pretty');
                const raw = document.getElementById('resp-raw');
                const respActive = document.getElementById('resp-active');

                tabs.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const t = btn.getAttribute('data-tab');
                        if (t === 'pretty') {
                            if (pretty) pretty.style.display = 'block';
                            if (raw) raw.style.display = 'none';
                            // Sinkronkan copy target
                            if (respActive) respActive.value = pretty?.innerText?.trim() || '';
                        } else {
                            if (pretty) pretty.style.display = 'none';
                            if (raw) raw.style.display = 'block';
                            const full = document.getElementById('resp-full');
                            const preRaw = document.querySelector('#resp-raw pre');
                            if (respActive) respActive.value = (full && !full.classList.contains(
                                'hidden')) ? full.value : (preRaw?.innerText || '');
                        }
                    });
                });

                // Expand raw
                const btnExpand = document.getElementById('btn-expand');
                const full = document.getElementById('resp-full');
                const preRaw = document.querySelector('#resp-raw pre');
                if (btnExpand && full && preRaw) {
                    btnExpand.addEventListener('click', () => {
                        full.classList.toggle('hidden');
                        btnExpand.innerHTML = full.classList.contains('hidden') ?
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg><span>Tampilkan seluruh response</span>' :
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg><span>Sembunyikan response penuh</span>';
                        // Update copy source jika tab raw aktif
                        const activeRaw = document.querySelector('.tab-btn[data-tab="raw"]');
                        if (activeRaw) {
                            respActive.value = full.classList.contains('hidden') ? (preRaw.innerText || '') :
                                full.value;
                        }
                    });
                }

                // Copy buttons
                document.querySelectorAll('[data-copy-target]').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const sel = btn.getAttribute('data-copy-target');
                        const el = document.querySelector(sel);
                        if (!el) return;
                        const txt = (el.tagName === 'TEXTAREA' || el.tagName === 'INPUT') ? el
                            .value : el.innerText;
                        try {
                            await navigator.clipboard.writeText(txt);
                            const old = btn.innerHTML;
                            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Copied!</span>';
                            setTimeout(() => btn.innerHTML = old, 1000);
                        } catch (e) {
                            alert('Gagal menyalin.');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
