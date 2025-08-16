<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Tickets</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-8">
                    <label class="block text-sm font-medium">Cari (ID/Subject/Order/Email/Nama)</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                           class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">Status</label>
                    @php $st = $filters['status'] ?? ''; @endphp
                    <select name="status"
                            class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                        <option value="">Semua</option>
                        <option value="open" @selected($st==='open')>Open</option>
                        <option value="closed" @selected($st==='closed')>Closed</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium invisible">&nbsp;</label>
                    <button class="h-10 w-full px-4 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                </div>
            </form>

            <div class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">User</th>
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
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $t->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $t->user->email ?? '' }}</div>
                                </td>
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
                                    <a href="{{ route('admin.tickets.show', $t) }}"
                                       class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="py-3 px-4" colspan="7">Belum ada tiket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
