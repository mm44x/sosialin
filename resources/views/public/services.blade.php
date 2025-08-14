@extends('layouts.marketing')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl md:text-3xl font-semibold">Layanan</h1>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('services.index') }}" class="mt-4 grid md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Cari layanan</label>
                <input type="text" name="q" value="{{ $selected['q'] ?? '' }}" placeholder="Ketik nama layanan…"
                    class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
            </div>

            <div>
                <label class="block text-sm font-medium">Kategori</label>
                <select name="category"
                    class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                    <option value="">Semua</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(($selected['category'] ?? '') == $c->id)>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Urutkan</label>
                <select name="sort"
                    class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                    <option value="">Nama (A–Z)</option>
                    <option value="price_asc" @selected(($selected['sort'] ?? '') === 'price_asc')>Harga termurah</option>
                    <option value="price_desc" @selected(($selected['sort'] ?? '') === 'price_desc')>Harga termahal</option>
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                <a href="{{ route('services.index') }}"
                    class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
            </div>
        </form>

        {{-- Daftar Layanan --}}
        <div class="mt-6 overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
            <table class="min-w-full text-sm">
                <thead class="text-left">
                    <tr>
                        <th class="py-2 px-4">Nama</th>
                        <th class="py-2 px-4">Kategori</th>
                        <th class="py-2 px-4">Harga /1000</th>
                        <th class="py-2 px-4">Min–Max</th>
                        <th class="py-2 px-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $s)
                        <tr class="border-t border-slate-200/60 dark:border-white/10">
                            <td class="py-2 px-4">
                                <div class="font-medium">{{ $s->public_name ?? $s->name }}</div>
                                @if (!empty($s->public_description))
                                    <div class="text-xs text-slate-500 dark:text-slate-300 line-clamp-2">
                                        {{ $s->public_description }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-2 px-4">{{ $s->category->name ?? '—' }}</td>
                            <td class="py-2 px-4">Rp {{ number_format($s->display_rate_per_thousand ?? 0, 2) }}</td>
                            <td class="py-2 px-4">{{ number_format($s->min) }}–{{ number_format($s->max) }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('orders.create', $s) }}"
                                    class="px-3 py-2 rounded-xl bg-primary text-white hover:opacity-90">Order</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-3 px-4" colspan="5">Tidak ada layanan yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $rows->links() }}</div>
    </div>
@endsection
