<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Riwayat Transaksi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                @if ($tx->isEmpty())
                    <p class="text-sm text-slateText dark:text-slate-300">Belum ada transaksi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left">
                                <tr>
                                    <th class="py-2 pr-4">Waktu</th>
                                    <th class="py-2 pr-4">Tipe</th>
                                    <th class="py-2 pr-4">Jumlah</th>
                                    <th class="py-2 pr-4">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="align-top">
                                @foreach ($tx as $t)
                                    <tr class="border-t border-slate-200/60 dark:border-white/10">
                                        <td class="py-2 pr-4">{{ $t->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-2 pr-4">
                                            <span
                                                class="px-2 py-1 rounded-lg text-xs
                        @class([
                            'bg-green-100 text-green-800' =>
                                $t->type === 'topup' || $t->type === 'refund',
                            'bg-red-100 text-red-800' => $t->type === 'order',
                        ])">
                                                {{ ucfirst($t->type) }}
                                            </span>
                                        </td>
                                        <td class="py-2 pr-4 font-semibold">
                                            @php $amt = (float) $t->amount; @endphp
                                            {{ $amt < 0 ? '-' : '' }}Rp {{ number_format(abs($amt), 2) }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            @php $note = $t->meta['note'] ?? ($t->meta['reason'] ?? null); @endphp
                                            {{ $note ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tx->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
