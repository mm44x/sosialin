<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Top Ups</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter --}}
            <form method="GET" class="mb-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium">Cari (Ref/ID/Email/Nama)</label>
                    <input type="text" name="q" value="{{ request('q', '') }}"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                        placeholder="mis. TP2025... atau email">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium">Status</label>
                    <select name="status"
                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                        @php $st = request('status',''); @endphp
                        <option value="">Semua</option>
                        @foreach (['pending', 'approved', 'rejected'] as $opt)
                            <option value="{{ $opt }}" @selected($st === $opt)>{{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium">Per halaman</label>
                    @php $pp=(int)request('per_page',20); @endphp
                    <select name="per_page"
                        class="mt-1 w-24 h-10 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                        @foreach ([10, 20, 30, 50] as $opt)
                            <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium invisible">&nbsp;</label>
                    <div class="mt-1 flex justify-end gap-2">
                        <button class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90">Terapkan</button>
                        <a href="{{ route('admin.topups.index') }}"
                            class="h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Reset</a>
                    </div>
                </div>
            </form>

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Ref</th>
                            <th class="py-2 px-4">User</th>
                            <th class="py-2 px-4">Amount</th>
                            <th class="py-2 px-4">Method</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Created</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $t)
                            @php
                                $badge = match ($t->status) {
                                    'approved' => 'bg-green-100 text-green-800 ring-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 ring-red-200',
                                    default => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                };
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $t->id }}</td>
                                <td class="py-2 px-4 font-medium">{{ $t->reference }}</td>
                                <td class="py-2 px-4">
                                    <div class="font-medium">{{ $t->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $t->user->email ?? '' }}</div>
                                </td>
                                <td class="py-2 px-4">Rp {{ number_format((float) $t->amount, 2) }}</td>
                                <td class="py-2 px-4">{{ strtoupper($t->method ?? '-') }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">{{ $t->created_at?->diffForHumans() }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.topups.show', $t) }}"
                                        class="px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="8">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
