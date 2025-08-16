<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Manajemen Tiket
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Kelola dan pantau semua tiket bantuan dari pengguna
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
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Cari (ID / Subject / Order ID / Email)
                            </label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                          bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                          focus:border-primary/20 dark:focus:border-primary/20
                                          transition-colors"
                                   placeholder="mis. 1234 atau email">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                            @php $st = $filters['status'] ?? ''; @endphp
                            <select name="status"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors">
                                <option value="">Semua Status</option>
                                @foreach (['open','pending','closed'] as $opt)
                                    <option value="{{ $opt }}" @selected($st===$opt)>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Dari Tanggal</label>
                            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                          bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                          focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                          focus:border-primary/20 dark:focus:border-primary/20
                                          transition-colors">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sampai Tanggal</label>
                            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                          bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                          focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                          focus:border-primary/20 dark:focus:border-primary/20
                                          transition-colors">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Per Halaman</label>
                            @php $pp = (int) ($filters['per_page'] ?? 20); @endphp
                            <select name="per_page"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors">
                                @foreach ([10,20,30,50] as $opt)
                                    <option value="{{ $opt }}" @selected($pp===$opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('admin.tickets.index') }}"
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

            {{-- Tickets Table --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Tiket</h3>
                        <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                            {{ $rows->total() }} tiket
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">#</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Subject</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Order ID</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">User</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Status</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Last Message</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse ($rows as $t)
                                @php 
                                    $badge = match($t->status){
                                        'open'    => 'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30',
                                        'pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30',
                                        'closed'  => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                        default   => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                    }; 
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-slate-600 dark:text-slate-400">#{{ $t->id }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $t->subject }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            Dibuat {{ $t->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($t->order_id)
                                            <span class="font-mono text-sm text-slate-600 dark:text-slate-400">#{{ $t->order_id }}</span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $t->user->name ?? '—' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 break-all">{{ $t->user->email ?? '' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                            {{ ucfirst($t->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($t->last_message_at)
                                            <div class="text-sm text-slate-600 dark:text-slate-400">{{ $t->last_message_at->diffForHumans() }}</div>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <a href="{{ route('admin.tickets.show', $t) }}"
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
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
                                            <span>Lihat</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada tiket</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada tiket yang ditemukan dengan filter saat ini.</p>
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