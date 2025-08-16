{{-- resources/views/wallet/transactions.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Riwayat Transaksi
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Pantau semua transaksi wallet Anda di satu tempat
                </p>
            </div>
        </div>
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

            {{-- Filter form --}}
            <form method="GET"
                class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-slate-300">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="mt-1 h-10 px-3 py-2 rounded-xl border w-full
                                bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-slate-300">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="mt-1 h-10 px-3 py-2 rounded-xl border w-full
                                bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-slate-300">Tipe Transaksi</label>
                        <select name="type"
                            class="mt-1 h-10 px-3 py-2 rounded-xl border w-full
                                bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="">Semua</option>
                            <option value="credit" @selected(request('type') === 'credit')>Credit</option>
                            <option value="debit" @selected(request('type') === 'debit')>Debit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-slate-300">Per halaman</label>
                        @php $pp = (int) request('per_page', 20); @endphp
                        <select name="per_page"
                            class="mt-1 h-10 px-3 py-2 rounded-xl border w-full
                                bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            @foreach ([10, 20, 30, 50] as $opt)
                                <option value="{{ $opt }}" @selected($pp === $opt)>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <a href="{{ route('wallet.transactions') }}"
                        class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-600 
                               text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 
                               inline-flex items-center">
                        Reset
                    </a>
                    <button type="submit"
                        class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90 
                               inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>

            {{-- Tabel transaksi --}}
            {{-- <div wire:loading.delay class="w-full h-12 flex items-center justify-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
            </div> --}}

            <div wire:loading.remove
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="py-3 px-4 font-semibold text-slate-900 dark:text-white">#</th>
                            <th class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    Tanggal
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}"
                                        class="hover:text-primary">
                                        @if (request('sort') === 'date')
                                            @if (request('order') === 'asc')
                                                ↑
                                            @else
                                                ↓
                                            @endif
                                        @else
                                            ↕
                                        @endif
                                    </a>
                                </div>
                            </th>
                            <th class="py-3 px-4 font-semibold text-slate-900 dark:text-white">Tipe</th>
                            <th class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    Amount
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}"
                                        class="hover:text-primary">
                                        @if (request('sort') === 'amount')
                                            @if (request('order') === 'asc')
                                                ↑
                                            @else
                                                ↓
                                            @endif
                                        @else
                                            ↕
                                        @endif
                                    </a>
                                </div>
                            </th>
                            <th class="py-3 px-4 font-semibold text-slate-900 dark:text-white">Keterangan</th>
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

                                // Menentukan style berdasarkan tipe transaksi
                                switch ($type) {
                                    case 'topup':
                                        $badgeClasses =
                                            'bg-green-100 text-green-800 ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30';
                                        break;
                                    case 'refund':
                                        $badgeClasses =
                                            'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30';
                                        break;
                                    case 'credit':
                                        $badgeClasses =
                                            'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-400/30';
                                        break;
                                    case 'debit':
                                        $badgeClasses =
                                            'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30';
                                        break;
                                    default:
                                        $badgeClasses =
                                            'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30';
                                }

                                $isCredit = in_array($type, ['credit', 'topup', 'refund']);
                                $note = $t->description ?? ($t->meta['reason'] ?? ($t->meta['note'] ?? '—'));
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
                                <td class="py-8 px-4 text-center" colspan="5">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="text-slate-400 dark:text-slate-500">
                                            <svg class="w-16 h-16 mx-auto" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <div class="text-lg font-medium text-slate-700 dark:text-slate-200">
                                            Belum ada transaksi
                                        </div>
                                        <p class="text-slate-600 dark:text-slate-400">
                                            Riwayat transaksi Anda akan muncul di sini ketika Anda mulai melakukan
                                            transaksi.
                                        </p>
                                    </div>
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
