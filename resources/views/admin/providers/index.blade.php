<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Providers</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex gap-2">
                <input name="search" value="{{ $search }}" placeholder="Cari nama provider..."
                    class="px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Cari</button>
                <a href="{{ route('admin.providers.index') }}"
                    class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
            </form>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#ID</th>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Base URL</th>
                            <th class="py-2 px-4">Markup %</th>
                            <th class="py-2 px-4">Active</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $p)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $p->id }}</td>
                                <td class="py-2 px-4 font-medium">{{ $p->name }}</td>
                                <td class="py-2 px-4">{{ $p->base_url }}</td>
                                <td class="py-2 px-4">{{ number_format((float) $p->markup_percent, 2) }}</td>
                                <td class="py-2 px-4">
                                    <span @class([
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        'bg-green-100 text-green-800 ring-green-200' => $p->active,
                                        'bg-red-100 text-red-800 ring-red-200' => !$p->active,
                                    ])>{{ $p->active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.providers.edit', $p) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="6">Belum ada provider.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
