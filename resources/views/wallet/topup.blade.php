<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Top Up Saldo</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Instruksi Pembayaran --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="font-semibold mb-2">Instruksi Pembayaran</div>

                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500 dark:text-slate-300 mb-1">Rekening Bank</div>
                        <ul class="space-y-1">
                            @foreach ($paymentInfo['bank_accounts'] ?? [] as $acc)
                                <li
                                    class="p-3 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                                    <div class="font-medium">{{ $acc['bank'] ?? '' }}</div>
                                    <div>{{ $acc['name'] ?? '' }}</div>
                                    <div class="text-slate-500">{{ $acc['number'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <div class="text-slate-500 dark:text-slate-300 mb-1">QRIS</div>
                        @if (!empty($paymentInfo['qris_image']))
                            <img src="{{ asset($paymentInfo['qris_image']) }}" alt="QRIS"
                                class="rounded-xl ring-1 ring-slate-200/60 dark:ring-white/10">
                        @else
                            <div class="text-slate-500">QRIS belum tersedia.</div>
                        @endif
                    </div>
                </div>

                @if (!empty($paymentInfo['note_html']))
                    <div class="mt-4 text-xs text-slate-600 dark:text-slate-300">{!! $paymentInfo['note_html'] !!}</div>
                @endif
            </div>

            {{-- Form Topup --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('wallet.topup.store') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Nominal (IDR)</label>
                        <input type="number" name="amount" inputmode="decimal" step="0.01" min="1000"
                            value="{{ old('amount') }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                            placeholder="contoh: 50000" required>
                        @error('amount')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Metode</label>
                        <select name="method"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <option value="">Pilih</option>
                            <option value="bank" @selected(old('method') === 'bank')>Transfer Bank</option>
                            <option value="qris" @selected(old('method') === 'qris')>QRIS</option>
                            <option value="other" @selected(old('method') === 'other')>Lainnya</option>
                        </select>
                        @error('method')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Bukti Pembayaran (jpg/png/webp/pdf, max 5MB)</label>
                        <input type="file" name="proof" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                            class="mt-1 block w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-xl
                                      file:border file:bg-white dark:file:bg-slate-800 dark:file:text-white
                                      file:border-slate-300 dark:file:border-slate-600">
                        @error('proof')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Catatan (opsional)</label>
                        <textarea name="note" rows="3"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                            placeholder="Tambahkan catatan bila perlu">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('dashboard') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">
                            Kirim Request Top Up
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
