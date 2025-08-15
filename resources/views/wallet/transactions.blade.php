{{-- resources/views/wallet/transactions.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white leading-tight">
            Riwayat Transaksi
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            {{-- Ringkasan saldo --}}
            <div class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-600 dark:text-slate-300">Saldo Anda</div>
                    <div class="text-lg font-semibold">Rp {{ number_format($balance ?? 0, 2) }}</div>
                </div>
            </div>

            {{-- Filter sederhana: per halaman --}}
            <form method="GET" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm font-medium">Per halaman</label>
                    @php $pp = (int) request('per_page', 20); @endphp
                    <select name="per_page"
                        class="mt-1 h-10 px-3 py-2 rounded-xl border
                                   bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600 w-28">
                        @foreach ([10, 20, 30, 50] as $opt)
                            <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90">
                    Terapkan
                </button>
            </form>

            {{-- Tabel transaksi --}}
            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Tanggal</th>
                            <th class="py-2 px-4">Tipe</th>
                            <th class="py-2 px-4">Amount</th>
                            <th class="py-2 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // $rows = paginator dari controller
                        @endphp

                        @forelse ($rows as $t)
                            @php
                                $type = strtolower($t->type ?? '');
                                $amount = (float) ($t->amount ?? 0);
                                $isCredit = $type === 'credit';
                                $badgeClasses = $isCredit
                                    ? 'bg-green-100 text-green-800 ring-green-200'
                                    : 'bg-red-100 text-red-800 ring-red-200';

                                $note = $t->description ?? ($t->meta['reason'] ?? ($t->meta['note'] ?? null ?? '—'));
                            @endphp
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4">#{{ $t->id }}</td>
                                <td class="py-2 px-4">{{ optional($t->created_at)->toDayDateTimeString() }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        class="inline-block px-2 py-1 rounded-lg text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                                        {{ ucfirst($type ?: '—') }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 font-medium">
                                    {{ $isCredit ? '+' : '-' }} Rp {{ number_format(abs($amount), 2) }}
                                </td>
                                <td class="py-2 px-4">
                                    <div class="max-w-[48ch] break-words text-slate-700 dark:text-slate-200">
                                        {{ $note }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-4 text-center text-slate-600 dark:text-slate-300" colspan="5">
                                    Belum ada transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div>{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
