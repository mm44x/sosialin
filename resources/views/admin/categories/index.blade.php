<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Categories</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Provider</th>
                            <th class="py-2 px-4">Aktif</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">{{ $r->name }}</td>
                                <td class="py-2 px-4">{{ $r->provider->name ?? '—' }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-2 py-1 rounded-lg text-xs @class([
                                        'bg-green-100 text-green-800' => $r->active,
                                        'bg-red-100 text-red-800' => !$r->active,
                                    ])">
                                        {{ $r->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4"><a href="{{ route('admin.categories.edit', $r) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
