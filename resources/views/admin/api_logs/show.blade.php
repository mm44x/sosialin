<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Admin — API Log
            #{{ $log->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Waktu</dt>
                        <dd class="font-medium">{{ $log->created_at->format('d M Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Provider</dt>
                        <dd class="font-medium">{{ $log->provider->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Endpoint</dt>
                        <dd class="font-medium">{{ $log->endpoint }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Status Code</dt>
                        <dd class="font-medium">{{ $log->status_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-slateText dark:text-slate-300">Durasi</dt>
                        <dd class="font-medium">{{ number_format($log->duration_ms) }} ms</dd>
                    </div>
                </dl>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <h3 class="font-semibold mb-2">Request</h3>
                    <pre class="text-xs overflow-x-auto">{{ $log->request_pretty }}</pre>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <h3 class="font-semibold mb-2">Response</h3>
                    <pre class="text-xs overflow-x-auto">{{ $log->response_pretty }}</pre>
                </div>
            </div>

            <div>
                <a href="{{ route('admin.api_logs.index') }}"
                    class="inline-block mt-2 px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
