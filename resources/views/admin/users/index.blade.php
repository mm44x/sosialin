<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Users</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter bar --}}
            <form method="GET" class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Cari (ID / Email / Nama)</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                            placeholder="mis. 1001 atau email">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Urutkan</label>
                        @php $sort = $filters['sort'] ?? 'id_desc'; @endphp
                        <select name="sort"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="id_desc" @selected($sort === 'id_desc')>Terbaru</option>
                            <option value="id_asc" @selected($sort === 'id_asc')>Terlama</option>
                            <option value="name_asc" @selected($sort === 'name_asc')>Nama (A→Z)</option>
                            <option value="balance_desc" @selected($sort === 'balance_desc')>Saldo Tertinggi</option>
                            <option value="orders_desc" @selected($sort === 'orders_desc')>Orders Terbanyak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Per halaman</label>
                        @php $pp = (int) ($filters['per_page'] ?? 20); @endphp
                        <select name="per_page"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            @foreach ([10, 20, 30, 50] as $opt)
                                <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="h-10 inline-flex items-center justify-center px-4 rounded-xl bg-primary text-white hover:opacity-90 whitespace-nowrap">
                                Terapkan
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                                class="h-10 inline-flex items-center justify-center px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Email</th>
                            <th class="py-2 px-4 text-right">Saldo</th>
                            <th class="py-2 px-4 text-right">Orders</th>
                            <th class="py-2 px-4">Created</th>
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $u)
                            @php
                                $balance = (float) (optional($u->wallet)->balance ?? 0);
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $u->id }}</td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $u->name }}</div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="text-sm">{{ $u->email }}</div>
                                </td>
                                <td class="py-2 px-4 text-right tabular-nums">Rp {{ number_format($balance, 2) }}</td>
                                <td class="py-2 px-4 text-right tabular-nums">{{ (int) ($u->orders_count ?? 0) }}</td>
                                <td class="py-2 px-4">{{ optional($u->created_at)->diffForHumans() }}</td>
                                <td class="py-2 px-4">{{ optional($u->updated_at)->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.orders.index', ['q' => $u->email]) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                        Lihat order
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="8">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $rows->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
