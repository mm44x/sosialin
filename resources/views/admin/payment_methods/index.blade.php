<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Metode Pembayaran</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex justify-end">
                <a href="{{ route('admin.payment-methods.create') }}"
                    class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Tambah Metode</a>
            </div>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left">
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Jenis</th>
                            <th class="py-2 px-4">Active</th>
                            <th class="py-2 px-4">Urutan</th>
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $m)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $m->id }}</td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $m->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $m->display_label }}</div>
                                </td>
                                <td class="py-2 px-4">{{ strtoupper($m->type) }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        class="inline-block px-2 py-1 rounded-lg text-xs ring-1 ring-inset
                                        {{ $m->is_active ? 'bg-green-100 text-green-800 ring-green-200' : 'bg-slate-100 text-slate-800 ring-slate-200' }}">
                                        {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">{{ $m->sort_order }}</td>
                                <td class="py-2 px-4">{{ optional($m->updated_at)->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.payment-methods.edit', $m) }}"
                                            class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Edit</a>
                                        <form method="POST" action="{{ route('admin.payment-methods.destroy', $m) }}"
                                            onsubmit="return confirm('Hapus metode ini?')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 px-4">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
