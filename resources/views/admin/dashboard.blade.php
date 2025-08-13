<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Admin — Dashboard</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 rounded-xl bg-green-50 text-green-800 ring-1 ring-green-200">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Kartu metrik --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-slateText dark:text-slate-300 text-sm">Total Pengguna</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($stats['users_total']) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-slateText dark:text-slate-300 text-sm">Total Saldo Wallet</div>
                    <div class="text-3xl font-bold mt-1">Rp {{ number_format($stats['wallet_total'], 2) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-slateText dark:text-slate-300 text-sm">Order Pending/Processing</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($stats['orders_pending_now']) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-slateText dark:text-slate-300 text-sm">Order Hari Ini</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($stats['orders_today']) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-slateText dark:text-slate-300 text-sm">Revenue Hari Ini</div>
                    <div class="text-3xl font-bold mt-1">Rp {{ number_format($stats['revenue_today'], 2) }}</div>
                </div>
                <div class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="text-slateText dark:text-slate-300 text-sm">Completed (24 jam)</div>
                    <div class="text-3xl font-bold mt-1">{{ number_format($stats['completed_24h']) }}</div>
                </div>
            </div>

            {{-- Daftar singkat --}}
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Order Terbaru</h3>
                        <a href="{{ route('orders.index') }}" class="text-sm text-primary hover:underline">Lihat
                            semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left">
                                <tr>
                                    <th class="py-2 pr-3">ID</th>
                                    <th class="py-2 pr-3">User</th>
                                    <th class="py-2 pr-3">Layanan</th>
                                    <th class="py-2 pr-3">Biaya</th>
                                    <th class="py-2 pr-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestOrders as $o)
                                    <tr class="border-t border-slate-200/60 dark:border-white/10">
                                        <td class="py-2 pr-3">#{{ $o->id }}</td>
                                        <td class="py-2 pr-3">{{ $o->user->name ?? '—' }}</td>
                                        <td class="py-2 pr-3">{{ $o->service->name ?? '—' }}</td>
                                        <td class="py-2 pr-3">Rp {{ number_format($o->cost, 2) }}</td>
                                        <td class="py-2 pr-3">
                                            @php $st = strtolower($o->status ?? ''); @endphp
                                            <span @class([
                                                'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
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
                                            ])>{{ ucfirst($st) ?: 'Unknown' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-3" colspan="5">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Pending / Processing</h3>
                        <a href="{{ route('orders.index') }}" class="text-sm text-primary hover:underline">Lihat
                            semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left">
                                <tr>
                                    <th class="py-2 pr-3">ID</th>
                                    <th class="py-2 pr-3">User</th>
                                    <th class="py-2 pr-3">Layanan</th>
                                    <th class="py-2 pr-3">Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingOrders as $o)
                                    <tr class="border-t border-slate-200/60 dark:border-white/10">
                                        <td class="py-2 pr-3">#{{ $o->id }}</td>
                                        <td class="py-2 pr-3">{{ $o->user->name ?? '—' }}</td>
                                        <td class="py-2 pr-3">{{ $o->service->name ?? '—' }}</td>
                                        <td class="py-2 pr-3">{{ $o->updated_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-3" colspan="4">Tidak ada order menunggu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <h3 class="font-semibold mb-3">User Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left">
                            <tr>
                                <th class="py-2 pr-3">ID</th>
                                <th class="py-2 pr-3">Nama</th>
                                <th class="py-2 pr-3">Email</th>
                                <th class="py-2 pr-3">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestUsers as $u)
                                <tr class="border-t border-slate-200/60 dark:border-white/10">
                                    <td class="py-2 pr-3">{{ $u->id }}</td>
                                    <td class="py-2 pr-3">{{ $u->name }}</td>
                                    <td class="py-2 pr-3">{{ $u->email }}</td>
                                    <td class="py-2 pr-3">{{ $u->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-3" colspan="4">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
