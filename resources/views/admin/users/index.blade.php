<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Manajemen Pengguna
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Kelola dan pantau semua pengguna dalam sistem
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Section --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Filter & Pencarian</h3>
                </div>

                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                        {{-- Cari --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Cari (ID / Email / Nama)
                            </label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="mis. 1001 atau email">
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Role</label>
                            @php $roleF = $filters['role'] ?? ''; @endphp
                            <select name="role"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="" @selected($roleF === '')>Semua Role</option>
                                <option value="user" @selected($roleF === 'user')>User</option>
                                <option value="admin" @selected($roleF === 'admin')>Admin</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                            @php $statusF = $filters['status'] ?? ''; @endphp
                            <select name="status"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="" @selected($statusF === '')>Semua Status</option>
                                <option value="active" @selected($statusF === 'active')>Active</option>
                                <option value="banned" @selected($statusF === 'banned')>Banned</option>
                            </select>
                        </div>

                        {{-- Urutkan --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Urutkan</label>
                            @php $sort = $filters['sort'] ?? 'id_desc'; @endphp
                            <select name="sort"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="id_desc" @selected($sort === 'id_desc')>Terbaru</option>
                                <option value="id_asc" @selected($sort === 'id_asc')>Terlama</option>
                                <option value="name_asc" @selected($sort === 'name_asc')>Nama (A→Z)</option>
                                <option value="balance_desc" @selected($sort === 'balance_desc')>Saldo Tertinggi</option>
                                <option value="orders_desc" @selected($sort === 'orders_desc')>Orders Terbanyak</option>
                            </select>
                        </div>

                        {{-- Per halaman --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Per Halaman</label>
                            @php $pp = (int) ($filters['per_page'] ?? 20); @endphp
                            <select name="per_page"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                @foreach ([10, 20, 30, 50] as $opt)
                                    <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                  border border-slate-200 dark:border-slate-700
                                  hover:border-primary/20 dark:hover:border-primary/20 
                                  hover:bg-primary/5 dark:hover:bg-primary/5
                                  text-slate-700 dark:text-slate-300
                                  transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reset Filter</span>
                        </a>
                        <a href="{{ route('admin.users.export', request()->query()) }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                  border border-slate-200 dark:border-slate-700
                                  hover:border-primary/20 dark:hover:border-primary/20 
                                  hover:bg-primary/5 dark:hover:bg-primary/5
                                  text-slate-700 dark:text-slate-300
                                  transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export CSV</span>
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                       bg-gradient-to-r from-primary to-purple-600 
                                       text-white font-medium shadow-sm 
                                       hover:shadow-md transition-all duration-300 hover:scale-105
                                       focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Terapkan Filter</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Users Table --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Pengguna</h3>
                        <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                            {{ $rows->total() }} pengguna
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Nama</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Email</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Role / Status</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Saldo</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Orders</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Created</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Updated</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse ($rows as $u)
                                @php
                                    $balance = (float) optional($u->wallet)->balance ?? 0;
                                    $isActive = (int) $u->is_active === 1;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-slate-600 dark:text-slate-400">#{{ $u->id }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $u->name }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400 break-all">{{ $u->email }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex flex-wrap items-center gap-2">
                                            {{-- Role badge --}}
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset
                                                     {{ $u->role === 'admin'
                                                         ? 'bg-purple-100 text-purple-800 ring-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:ring-purple-400/30'
                                                         : 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30' }}">
                                                {{ ucfirst($u->role) }}
                                            </span>
                                            {{-- Status badge --}}
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset
                                                     {{ $isActive 
                                                         ? 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30' 
                                                         : 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30' }}">
                                                {{ $isActive ? 'Active' : 'Banned' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-slate-600 dark:text-slate-400">
                                            Rp {{ number_format($balance, 2) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ (int) ($u->orders_count ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ optional($u->created_at)->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ optional($u->updated_at)->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.users.show', $u) }}"
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl 
                                                       border border-slate-200 dark:border-slate-700
                                                       hover:border-primary/20 dark:hover:border-primary/20 
                                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                                       text-slate-700 dark:text-slate-300
                                                       transition-all duration-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Detail</span>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $u) }}"
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl 
                                                       border border-slate-200 dark:border-slate-700
                                                       hover:border-primary/20 dark:hover:border-primary/20 
                                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                                       text-slate-700 dark:text-slate-300
                                                       transition-all duration-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </a>
                                            <a href="{{ route('admin.orders.index', ['q' => $u->email]) }}"
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl 
                                                       border border-slate-200 dark:border-slate-700
                                                       hover:border-primary/20 dark:hover:border-primary/20 
                                                       hover:bg-primary/5 dark:hover:bg-primary/5
                                                       text-slate-700 dark:text-slate-300
                                                       transition-all duration-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                                <span>Orders</span>
                                            </a>

                                            {{-- Ban / Unban cepat --}}
                                            <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}"
                                                onsubmit="return confirm('{{ $isActive ? 'Nonaktifkan (ban)' : 'Aktifkan (unban)' }} user ini?');"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl 
                                                           border border-slate-200 dark:border-slate-700
                                                           hover:border-primary/20 dark:hover:border-primary/20 
                                                           hover:bg-primary/5 dark:hover:bg-primary/5
                                                           text-slate-700 dark:text-slate-300
                                                           transition-all duration-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="{{ $isActive ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                                                    </svg>
                                                    <span>{{ $isActive ? 'Ban' : 'Unban' }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada pengguna</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada pengguna yang ditemukan dengan filter saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($rows->hasPages())
                <div class="flex justify-center">
                    <div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 rounded-2xl p-4">
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
