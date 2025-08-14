<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Users</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter bar --}}
            <form method="GET" class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                    {{-- Cari --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Cari (ID / Email / Nama)</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                      bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                            placeholder="mis. 1001 atau email">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium">Role</label>
                        @php $roleF = $filters['role'] ?? ''; @endphp
                        <select name="role"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                       bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="" @selected($roleF === '')>Semua</option>
                            <option value="user" @selected($roleF === 'user')>User</option>
                            <option value="admin" @selected($roleF === 'admin')>Admin</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium">Status</label>
                        @php $statusF = $filters['status'] ?? ''; @endphp
                        <select name="status"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                       bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="" @selected($statusF === '')>Semua</option>
                            <option value="active" @selected($statusF === 'active')>Active</option>
                            <option value="banned" @selected($statusF === 'banned')>Banned</option>
                        </select>
                    </div>

                    {{-- Urutkan --}}
                    <div>
                        <label class="block text-sm font-medium">Urutkan</label>
                        @php $sort = $filters['sort'] ?? 'id_desc'; @endphp
                        <select name="sort"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                       bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="id_desc" @selected($sort === 'id_desc')>Terbaru</option>
                            <option value="id_asc" @selected($sort === 'id_asc')>Terlama</option>
                            <option value="name_asc" @selected($sort === 'name_asc')>Nama (A→Z)</option>
                            <option value="balance_desc" @selected($sort === 'balance_desc')>Saldo Tertinggi</option>
                            <option value="orders_desc" @selected($sort === 'orders_desc')>Orders Terbanyak</option>
                        </select>
                    </div>

                    {{-- Per halaman --}}
                    <div>
                        <label class="block text-sm font-medium">Per halaman</label>
                        @php $pp = (int) ($filters['per_page'] ?? 20); @endphp
                        <select name="per_page"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                       bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            @foreach ([10, 20, 30, 50] as $opt)
                                <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tombol aksi: Terapkan / Reset / Export CSV --}}
                <div class="mt-3 flex items-center gap-2">
                    <button type="submit"
                        class="h-10 inline-flex items-center justify-center px-4 rounded-xl
                                   bg-primary text-white hover:opacity-90 whitespace-nowrap">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="h-10 inline-flex items-center justify-center px-4 rounded-xl border
                              dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                        Reset
                    </a>
                    <a href="{{ route('admin.users.export', request()->query()) }}"
                        class="h-10 inline-flex items-center justify-center px-4 rounded-xl border
                              dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                        Export
                    </a>
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
                            <th class="py-2 px-4">Role / Status</th>
                            <th class="py-2 px-4">Saldo</th>
                            <th class="py-2 px-4">Orders</th>
                            <th class="py-2 px-4">Created</th>
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $u)
                            @php
                                $balance = (float) optional($u->wallet)->balance ?? 0;
                                $isActive = (int) $u->is_active === 1;
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $u->id }}</td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $u->name }}</div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="text-sm break-all">{{ $u->email }}</div>
                                </td>
                                <td class="py-2 px-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- Role badge --}}
                                        <span
                                            class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset
                                                     {{ $u->role === 'admin'
                                                         ? 'bg-purple-100 text-purple-800 ring-purple-200'
                                                         : 'bg-slate-100 text-slate-800 ring-slate-200' }}">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                        {{-- Status badge --}}
                                        <span
                                            class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset
                                                     {{ $isActive ? 'bg-green-100 text-green-800 ring-green-200' : 'bg-red-100 text-red-800 ring-red-200' }}">
                                            {{ $isActive ? 'Active' : 'Banned' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-2 px-4">Rp {{ number_format($balance, 2) }}</td>
                                <td class="py-2 px-4">{{ (int) ($u->orders_count ?? 0) }}</td>
                                <td class="py-2 px-4">{{ optional($u->created_at)->diffForHumans() }}</td>
                                <td class="py-2 px-4">{{ optional($u->updated_at)->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.users.show', $u) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.users.edit', $u) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                                            Edit
                                        </a>
                                        <a href="{{ route('admin.orders.index', ['q' => $u->email]) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                                            Lihat order
                                        </a>

                                        {{-- Ban / Unban cepat --}}
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}"
                                            onsubmit="return confirm('{{ $isActive ? 'Nonaktifkan (ban)' : 'Aktifkan (unban)' }} user ini?');">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 whitespace-nowrap">
                                                {{ $isActive ? 'Ban' : 'Unban' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="9">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
