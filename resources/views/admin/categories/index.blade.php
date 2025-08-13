<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Categories</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="p-3 rounded-xl bg-green-50 text-green-800 ring-1 ring-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET"
                class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 grid gap-3 md:grid-cols-4">
                {{-- Cari nama --}}
                <div>
                    <label class="block text-xs text-slateText dark:text-slate-300">Cari nama</label>
                    <input name="search" value="{{ $filters['search'] }}"
                        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                        placeholder="Nama kategori...">
                </div>

                {{-- Provider --}}
                <div>
                    <label class="block text-xs text-slateText dark:text-slate-300">Provider</label>
                    <select name="provider"
                        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        <option value="">— Semua —</option>
                        @foreach ($providers as $p)
                            <option value="{{ $p->id }}" @selected(($filters['provider'] ?? '') == $p->id)>{{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Aksi: label tak terlihat untuk menyamakan tinggi kolom, lalu tombol sejajar --}}
                <div class="md:col-span-2 md:self-end">
                    <label class="block text-xs invisible">Aksi</label>
                    <div class="flex gap-2">
                        <button
                            class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 h-[42px] inline-flex items-center justify-center">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.categories.index') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10 h-[42px] inline-flex items-center justify-center">
                            Reset
                        </a>
                    </div>
                </div>
            </form>


            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#ID</th>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Provider</th>
                            <th class="py-2 px-4">Active</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $c)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $c->id }}</td>
                                <td class="py-2 px-4 font-medium">{{ $c->name }}</td>
                                <td class="py-2 px-4">{{ $c->provider->name ?? '—' }}</td>
                                <td class="py-2 px-4">
                                    <span @class([
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        'bg-green-100 text-green-800 ring-green-200' => $c->active,
                                        'bg-red-100 text-red-800 ring-red-200' => !$c->active,
                                    ])>{{ $c->active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.categories.edit', $c) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="5">Tidak ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
