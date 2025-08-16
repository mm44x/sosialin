<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Manajemen Services
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Kelola dan pantau semua layanan dari berbagai provider
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
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Cari Nama Layanan
                            </label>
                            <input name="search" value="{{ request('search') }}" 
                                placeholder="Cari nama layanan..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Provider</label>
                            <select name="provider"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="">Semua Provider</option>
                                @foreach ($providers as $p)
                                    <option value="{{ $p->id }}" @selected(request('provider') == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                            <select name="category"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Aksi</label>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl 
                                           bg-gradient-to-r from-primary to-purple-600 
                                           text-white font-medium shadow-sm 
                                           hover:shadow-md transition-all duration-300 hover:scale-105
                                           focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Services Table --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar Services</h3>
                        <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                            {{ $rows->total() }} services
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Nama</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Provider</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Kategori</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Rate/1000</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Markup Override</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Status</th>
                                <th class="py-4 px-6 text-left font-semibold text-slate-700 dark:text-slate-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                            @forelse ($rows as $s)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $s->name }}</div>
                                        @if($s->public_name && $s->public_name !== $s->name)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                Publik: {{ $s->public_name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $s->provider->name ?? '—' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $s->category->name ?? '—' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-sm text-slate-600 dark:text-slate-400">
                                            $ {{ number_format($s->rate, 4) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $s->markup_percent_override === null ? '—' : number_format($s->markup_percent_override, 2) . '%' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset
                                                   {{ $s->active 
                                                       ? 'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30' 
                                                       : 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30' }}">
                                                {{ $s->active ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if($s->public_active)
                                                <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-medium ring-1 ring-inset
                                                       bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30">
                                                    Public
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <a href="{{ route('admin.services.edit', $s) }}"
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl 
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 px-6 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada services</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada services yang ditemukan dengan filter saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($rows->hasPages())
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mt-6 px-4 sm:px-0">
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
