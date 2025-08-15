@extends('layouts.marketing')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1
                    class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Layanan Kami
                </h1>
                <p class="mt-2 text-slate-600 dark:text-slate-400">
                    Pilih dari berbagai layanan social media marketing yang kami sediakan
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $rows->total() }} layanan tersedia
                </span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div
            class="p-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
            <form method="GET" action="{{ route('services.index') }}" class="grid md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Cari layanan</label>
                    <div class="mt-1 relative">
                        <input type="text" name="q" value="{{ $selected['q'] ?? '' }}"
                            placeholder="Ketik nama layanan..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700
                                      bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                      placeholder-slate-400 dark:placeholder-slate-500
                                      focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                      focus:border-primary/20 dark:focus:border-primary/20
                                      transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kategori</label>
                    <select name="category"
                        class="mt-1 w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 
                               bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                               focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20
                               focus:border-primary/20 dark:focus:border-primary/20
                               transition-colors">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}" @selected(($selected['category'] ?? '') == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Urutkan</label>
                    <select name="sort"
                        class="mt-1 w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 
                               bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                               focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20
                               focus:border-primary/20 dark:focus:border-primary/20
                               transition-colors">
                        <option value="">Nama (A–Z)</option>
                        <option value="price_asc" @selected(($selected['sort'] ?? '') === 'price_asc')>Harga termurah</option>
                        <option value="price_desc" @selected(($selected['sort'] ?? '') === 'price_desc')>Harga termahal</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex gap-2">
                    <button
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary to-purple-600 text-white
                                 font-medium shadow-sm hover:shadow-md hover:scale-105 transition-all duration-300
                                 focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Terapkan Filter</span>
                        </span>
                    </button>
                    <a href="{{ route('services.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700
                               hover:border-primary/20 dark:hover:border-primary/20 hover:bg-primary/5 
                               font-medium transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Daftar Layanan --}}
        <div class="mt-6 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div
                    class="overflow-hidden rounded-2xl bg-white dark:bg-slate-800/50 backdrop-blur-xl 
                            ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Nama Layanan
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Kategori
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Harga /1000
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Min–Max
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-right text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800/30">
                            @forelse ($rows as $s)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/70 transition-colors">
                                    <td class="whitespace-nowrap py-4 px-4">
                                        <div>
                                            <div class="font-medium text-slate-900 dark:text-white">
                                                {{ $s->public_name ?? $s->name }}
                                            </div>
                                            @if (!empty($s->public_description))
                                                <div class="mt-0.5 text-sm text-slate-500 dark:text-slate-400 line-clamp-2">
                                                    {{ $s->public_description }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap py-4 px-4 text-sm text-slate-600 dark:text-slate-300">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            {{ $s->category->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-4 px-4 text-sm text-slate-600 dark:text-slate-300">
                                        <span class="font-medium text-slate-900 dark:text-white">
                                            Rp {{ number_format($s->display_rate_per_thousand ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-4 px-4 text-sm text-slate-600 dark:text-slate-300">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                            {{ number_format($s->min) }}–{{ number_format($s->max) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-4 px-4 text-sm text-right">
                                        <a href="{{ route('orders.create', $s) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                                                   bg-gradient-to-r from-primary to-purple-600 
                                                   text-white font-medium shadow-sm 
                                                   hover:shadow-md transition-all duration-300 hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Order
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-8 px-4 text-center text-slate-500 dark:text-slate-400" colspan="5">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 12V8h-4V4h-4v4H8v4H4v4h4v4h4v-4h4v-4h4z" />
                                            </svg>
                                            <span class="font-medium">Tidak ada layanan yang cocok</span>
                                            <p class="text-sm text-slate-400">Coba ubah filter pencarian Anda</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $rows->links() }}
        </div>
    </div>
@endsection
