<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Tiket Bantuan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('tickets.create') }}"
                   class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Buat Tiket</a>
            </div>

            <div class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Subject</th>
                            <th class="py-2 px-4">Order</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Updated</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $t)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $t->id }}</td>
                                <td class="py-2 px-4">{{ $t->subject }}</td>
                                <td class="py-2 px-4">{{ $t->order_id ? ('#'.$t->order_id) : '—' }}</td>
                                @php $st = $t->status; @endphp
                                <td class="py-2 px-4">
                                    <span @class([
                                        'inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset',
                                        'bg-green-100 text-green-800 ring-green-200' => $st==='open',
                                        'bg-slate-100 text-slate-800 ring-slate-200' => $st!=='open',
                                    ])>{{ ucfirst($st) }}</span>
                                </td>
                                <td class="py-2 px-4">{{ $t->updated_at?->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('tickets.show', $t) }}"
                                       class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="py-3 px-4" colspan="6">Belum ada tiket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
