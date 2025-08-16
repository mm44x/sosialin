<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Tiket Bantuan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-end gap-3 flex-wrap">
                <form method="GET" class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Cari (ID/Subject/Order ID)</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                               class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                      bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                               placeholder="mis. 1201 atau subject">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Status</label>
                        @php $st = $filters['status'] ?? ''; @endphp
                        <select name="status"
                                class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                       bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="">Semua</option>
                            @foreach (['open','pending','closed'] as $opt)
                                <option value="{{ $opt }}" @selected($st===$opt)>{{ ucfirst($opt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Per halaman</label>
                        @php $pp = (int) ($filters['per_page'] ?? 20); @endphp
                        <select name="per_page"
                                class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                       bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            @foreach ([10,20,30,50] as $opt)
                                <option value="{{ $opt }}" @selected($pp===$opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="flex items-center gap-2 ml-auto">
                            <button class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                            <a href="{{ route('tickets.index') }}"
                               class="h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
                        </div>
                    </div>
                </form>

                <a href="{{ route('tickets.create') }}"
                   class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90">Buat Tiket</a>
            </div>

            <div class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                    <tr>
                        <th class="py-2 px-4">#</th>
                        <th class="py-2 px-4">Subject</th>
                        <th class="py-2 px-4">Order ID</th>
                        <th class="py-2 px-4">Status</th>
                        <th class="py-2 px-4">Update</th>
                        <th class="py-2 px-4"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $t)
                        @php $badge = match($t->status){
                            'open'    => 'bg-blue-100 text-blue-800 ring-blue-200',
                            'pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                            'closed'  => 'bg-slate-200 text-slate-800 ring-slate-300',
                            default   => 'bg-slate-100 text-slate-800 ring-slate-200',
                        }; @endphp
                        <tr class="border-t border-slate-200/60 dark:border-white/10">
                            <td class="py-2 px-4">#{{ $t->id }}</td>
                            <td class="py-2 px-4">{{ $t->subject }}</td>
                            <td class="py-2 px-4">{{ $t->order_id ?: '—' }}</td>
                            <td class="py-2 px-4">
                                <span class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4">{{ optional($t->last_message_at)->diffForHumans() ?: '—' }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('tickets.show', $t) }}"
                                   class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Lihat</a>
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