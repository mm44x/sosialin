<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — API Log #{{ $log->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div><span class="text-slate-500 dark:text-slate-300">Provider:</span> <span
                            class="font-medium">{{ $log->provider->name ?? '—' }}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-300">Endpoint:</span> <span
                            class="font-medium">{{ $log->endpoint }}</span></div>
                    <div><span class="text-slate-500 dark:text-slate-300">Status:</span>
                        <span @class([
                            'inline-block px-2 py-0.5 rounded-lg text-xs font-medium ring-1 ring-inset',
                            'bg-green-100 text-green-800 ring-green-200' =>
                                (int) $log->status_code >= 200 && (int) $log->status_code < 400,
                            'bg-red-100 text-red-800 ring-red-200' => !(
                                (int) $log->status_code >= 200 && (int) $log->status_code < 400
                            ),
                        ])>{{ $log->status_code }}</span>
                    </div>
                    <div><span class="text-slate-500 dark:text-slate-300">Durasi:</span> <span
                            class="font-medium">{{ number_format((int) $log->duration_ms) }} ms</span></div>
                    <div class="md:col-span-2">
                        <span class="text-slate-500 dark:text-slate-300">Waktu:</span>
                        <span class="font-medium">
                            {{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '—' }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- REQUEST --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Request</h3>
                    <button type="button"
                        class="px-3 py-1.5 rounded-lg border dark:border-slate-600 hover:bg-primary/10 text-sm"
                        data-copy-target="#req-json">Copy</button>
                </div>
                <pre id="req-json"
                    class="mt-3 overflow-auto text-xs p-3 rounded-lg bg-slate-50 dark:bg-black/30 ring-1 ring-slate-200/60 dark:ring-white/10">
{{ json_encode($reqData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                </pre>
            </div>

            {{-- RESPONSE --}}
            <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Response</h3>
                    <div class="flex gap-2">
                        <button type="button" data-tab="pretty"
                            class="tab-btn px-3 py-1.5 rounded-lg border dark:border-slate-600 hover:bg-primary/10 text-sm">Pretty</button>
                        <button type="button" data-tab="raw"
                            class="tab-btn px-3 py-1.5 rounded-lg border dark:border-slate-600 hover:bg-primary/10 text-sm">Raw</button>
                        <button type="button"
                            class="px-3 py-1.5 rounded-lg border dark:border-slate-600 hover:bg-primary/10 text-sm"
                            data-copy-target="#resp-active">Copy</button>
                    </div>
                </div>

                {{-- Pretty JSON --}}
                <pre id="resp-pretty"
                    class="resp-tab mt-3 overflow-auto text-xs p-3 rounded-lg bg-slate-50 dark:bg-black/30 ring-1 ring-slate-200/60 dark:ring-white/10"
                    style="display: {{ $respIsJson ? 'block' : 'none' }}">
@if ($respIsJson)
{{ json_encode($respArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
@else
{{-- not json --}}
@endif
                </pre>

                {{-- Raw --}}
                <div id="resp-raw" class="resp-tab mt-3" style="display: {{ $respIsJson ? 'none' : 'block' }}">
                    <pre id="resp-active"
                        class="overflow-auto text-xs p-3 rounded-lg bg-slate-50 dark:bg-black/30 ring-1 ring-slate-200/60 dark:ring-white/10">{{ $respPreview }}</pre>
                    @if ($respTruncated)
                        <button type="button" id="btn-expand"
                            class="mt-2 px-3 py-1.5 rounded-lg border dark:border-slate-600 hover:bg-primary/10 text-sm">
                            Tampilkan seluruh response
                        </button>
                        <textarea id="resp-full"
                            class="hidden w-full mt-2 text-xs p-3 rounded-lg border dark:border-slate-600 bg-slate-50 dark:bg-black/30"
                            rows="12">{{ $respRaw }}</textarea>
                    @endif
                </div>

                {{-- Hidden alias untuk Copy: saat tab Pretty aktif, targetkan ini --}}
                <textarea id="resp-active" class="hidden">{{ $respIsJson ? json_encode($respArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $respRaw }}</textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.api-logs.index') }}"
                    class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
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
                        btnExpand.textContent = full.classList.contains('hidden') ?
                            'Tampilkan seluruh response' : 'Sembunyikan response penuh';
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
                            const old = btn.textContent;
                            btn.textContent = 'Copied!';
                            setTimeout(() => btn.textContent = old, 1000);
                        } catch (e) {
                            alert('Gagal menyalin.');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
