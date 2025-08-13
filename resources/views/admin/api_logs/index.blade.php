<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Admin — API Logs</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET"
                class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 grid gap-3 sm:grid-cols-5">
                <select name="provider_id"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <option value="">— Semua Provider —</option>
                    @foreach ($providers as $p)
                        <option value="{{ $p->id }}" @selected(($filters['provider_id'] ?? '') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>

                <select name="endpoint"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    @php $endpoints = ['services','add','status','balance']; @endphp
                    <option value="">— Semua Endpoint —</option>
                    @foreach ($endpoints as $ep)
                        <option value="{{ $ep }}" @selected(($filters['endpoint'] ?? '') == $ep)>{{ $ep }}</option>
                    @endforeach
                </select>

                <input name="status_code" type="number" placeholder="Status Code"
                    value="{{ $filters['status_code'] ?? '' }}"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600" />

                <input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600" />
                <input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600" />

                <div class="sm:col-span-5 flex gap-2">
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Filter</button>
                    <a href="{{ route('admin.api_logs.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
                </div>
            </form>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">ID</th>
                            <th class="py-2 px-4">Waktu</th>
                            <th class="py-2 px-4">Provider</th>
                            <th class="py-2 px-4">Endpoint</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Durasi (ms)</th>
                            <th class="py-2 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $ok = (int) $log->status_code >= 200 && (int) $log->status_code < 400;
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4 font-medium">#{{ $log->id }}</td>
                                <td class="py-2 px-4">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                <td class="py-2 px-4">{{ $log->provider->name ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $log->endpoint }}</td>
                                <td class="py-2 px-4">
                                    <span @class([
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        'bg-green-100 text-green-800 ring-green-200' => $ok,
                                        'bg-red-100 text-red-800 ring-red-200' => !$ok,
                                    ])>
                                        {{ $log->status_code }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">{{ number_format($log->duration_ms) }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.api_logs.show', $log) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="7">Tidak ada log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
