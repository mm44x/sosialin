<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Services</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap gap-2">
                <input name="search" value="{{ request('search') }}" placeholder="Cari nama layanan..."
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                <select name="provider"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <option value="">— Semua Provider —</option>
                    @foreach ($providers as $p)
                        <option value="{{ $p->id }}" @selected(request('provider') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                <select name="category"
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <option value="">— Semua Kategori —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded-xl bg-primary text-white">Filter</button>
            </form>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Provider</th>
                            <th class="py-2 px-4">Kategori</th>
                            <th class="py-2 px-4">Rate/1000</th>
                            <th class="py-2 px-4">Markup Override</th>
                            <th class="py-2 px-4">Aktif</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $s)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">{{ $s->name }}</td>
                                <td class="py-2 px-4">{{ $s->provider->name ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $s->category->name ?? '-' }}</td>
                                <td class="py-2 px-4">$ {{ number_format($s->rate, 4) }}</td>
                                <td class="py-2 px-4">
                                    {{ $s->markup_percent_override === null ? '—' : number_format($s->markup_percent_override, 2) . '%' }}
                                </td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded-lg text-xs @class([
                                        'bg-green-100 text-green-800' => $s->active,
                                        'bg-red-100 text-red-800' => !$s->active,
                                    ])">
                                        {{ $s->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.services.edit', $s) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
