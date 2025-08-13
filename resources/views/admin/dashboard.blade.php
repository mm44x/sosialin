<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter rentang tanggal --}}
            <form method="GET"
                class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 grid gap-3 sm:grid-cols-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs text-slateText dark:text-slate-300">Dari</label>
                    <input type="date" name="from" value="{{ $range['from'] }}"
                        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-slateText dark:text-slate-300">Sampai</label>
                    <input type="date" name="to" value="{{ $range['to'] }}"
                        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                </div>
                <div class="flex items-end">
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                </div>
            </form>

            {{-- KPI Cards --}}
            <div class="grid md:grid-cols-3 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Total User</div>
                    <div class="mt-1 text-2xl font-bold">{{ number_format($kpi['totalUsers']) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Total Saldo Wallet</div>
                    <div class="mt-1 text-2xl font-bold">Rp {{ number_format($kpi['totalSaldo'], 2) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Top-up ({{ $range['from'] }} →
                        {{ $range['to'] }})</div>
                    <div class="mt-1 text-2xl font-bold">Rp {{ number_format($kpi['topupSum'], 2) }}</div>
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Pending</div>
                    <div class="text-xl font-semibold">{{ number_format($kpi['pending']) }}</div>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Processing</div>
                    <div class="text-xl font-semibold">{{ number_format($kpi['processing']) }}</div>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Completed</div>
                    <div class="text-xl font-semibold">{{ number_format($kpi['completed']) }}</div>
                </div>
                <div class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-sm text-slateText dark:text-slate-300">Partial/Cancel/Error</div>
                    <div class="text-xl font-semibold">{{ number_format($kpi['error']) }}</div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Orders terbaru --}}
                <div
                    class="rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200/60 dark:border-white/10 font-semibold">10 Order
                        Terbaru</div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left">
                                <tr>
                                    <th class="py-2 px-4">#ID</th>
                                    <th class="py-2 px-4">User</th>
                                    <th class="py-2 px-4">Layanan</th>
                                    <th class="py-2 px-4">Qty</th>
                                    <th class="py-2 px-4">Biaya</th>
                                    <th class="py-2 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $o)
                                    <tr class="border-t border-slate-200/60 dark:border-white/10">
                                        <td class="py-2 px-4 font-medium">#{{ $o->id }}</td>
                                        <td class="py-2 px-4">{{ $o->user->name ?? '—' }}</td>
                                        <td class="py-2 px-4">{{ $o->service->name ?? '—' }}</td>
                                        <td class="py-2 px-4">{{ $o->quantity }}</td>
                                        <td class="py-2 px-4">Rp {{ number_format($o->cost, 2) }}</td>
                                        <td class="py-2 px-4">
                                            <span @class([
                                                'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                                'bg-yellow-100 text-yellow-800 ring-yellow-200' => in_array($o->status, [
                                                    'pending',
                                                    'processing',
                                                ]),
                                                'bg-green-100 text-green-800 ring-green-200' => $o->status === 'completed',
                                                'bg-orange-100 text-orange-800 ring-orange-200' => $o->status === 'partial',
                                                'bg-red-100 text-red-800 ring-red-200' => in_array($o->status, [
                                                    'canceled',
                                                    'error',
                                                ]),
                                            ])>{{ ucfirst($o->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-3 px-4" colspan="6">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- API Logs terbaru --}}
                <div
                    class="rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200/60 dark:border-white/10 font-semibold">10 API Logs
                        Terbaru</div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left">
                                <tr>
                                    <th class="py-2 px-4">#ID</th>
                                    <th class="py-2 px-4">Waktu</th>
                                    <th class="py-2 px-4">Provider</th>
                                    <th class="py-2 px-4">Endpoint</th>
                                    <th class="py-2 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentApiLogs as $log)
                                    @php $ok = (int)$log->status_code >= 200 && (int)$log->status_code < 400; @endphp
                                    <tr class="border-t border-slate-200/60 dark:border-white/10">
                                        <td class="py-2 px-4 font-medium">#{{ $log->id }}</td>
                                        <td class="py-2 px-4">{{ $log->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-2 px-4">{{ $log->provider->name ?? '—' }}</td>
                                        <td class="py-2 px-4">{{ $log->endpoint }}</td>
                                        <td class="py-2 px-4">
                                            <span @class([
                                                'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                                'bg-green-100 text-green-800 ring-green-200' => $ok,
                                                'bg-red-100 text-red-800 ring-red-200' => !$ok,
                                            ])>{{ $log->status_code }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-3 px-4" colspan="5">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
