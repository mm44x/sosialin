@extends('layouts.marketing')

@section('content')
    <section class="py-8">
        <div class="container mx-auto px-4 space-y-4">
            <h1 class="text-2xl md:text-3xl font-semibold">Layanan Tersedia</h1>

            {{-- Filter & Search (tanpa provider) --}}
            <form method="GET"
                class="grid gap-3 md:grid-cols-5 p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama layanan..."
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600 md:col-span-2">

                <select name="category_id"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <option value="">— Semua Kategori —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(($filters['category_id'] ?? '') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2 md:col-span-2">
                    <select name="sort"
                        class="flex-1 px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Nama (A–Z)</option>
                        <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Nama (Z–A)</option>
                        <option value="rate_asc" @selected(($filters['sort'] ?? '') === 'rate_asc')>Rate dasar ↑</option>
                        <option value="rate_desc" @selected(($filters['sort'] ?? '') === 'rate_desc')>Rate dasar ↓</option>
                    </select>
                    <select name="per_page"
                        class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        @foreach ([10, 20, 30, 50] as $pp)
                            <option value="{{ $pp }}" @selected((int) ($filters['per_page'] ?? 20) === $pp)>{{ $pp }}/hal
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-5 flex gap-2">
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                    <a href="{{ route('services.index') }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
                </div>
            </form>

            {{-- Daftar Layanan (tanpa kolom provider & tanpa teks markup) --}}
            <div class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Kategori</th>
                            <th class="py-2 px-4">Min–Max</th>
                            <th class="py-2 px-4">Harga / 1000</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $s)
                            @php
                                $rate1000 = $computed[$s->id]['ratePerThousand'] ?? null;
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $s->name }}</div>
                                    @if ($s->description)
                                        <div class="text-slateText dark:text-slate-300 text-xs mt-1">{{ $s->description }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 px-4">{{ $s->category->name ?? '—' }}</td>
                                <td class="py-2 px-4">{{ $s->min }}–{{ $s->max }}</td>
                                <td class="py-2 px-4">
                                    @if ($rate1000 !== null)
                                        <div class="font-semibold">Rp {{ number_format($rate1000, 2) }}</div>
                                    @else
                                        <span class="text-xs text-slateText dark:text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">
                                    @auth
                                        <a href="{{ route('orders.create', $s) }}"
                                            class="px-3 py-2 rounded-xl bg-primary text-white hover:opacity-90">Pesan</a>
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="5">Tidak ada layanan dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $rows->links() }}</div>
        </div>
    </section>
@endsection
