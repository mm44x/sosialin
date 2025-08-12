<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Top-up Saldo (Simulasi)</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="text-sm text-slateText dark:text-slate-300">Saldo Saat Ini</div>
                <div class="text-2xl font-bold">
                    Rp {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}
                </div>

                <form method="POST" action="{{ route('wallet.topup.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-sm font-medium">Jumlah Top-up (Rp)</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="1000"
                            value="{{ old('amount', 50000) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('amount')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-medium">Catatan (opsional)</label>
                        <input id="note" name="note" value="{{ old('note') }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        @error('note')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('dashboard') }}"
                            class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary">
                            Batal
                        </a>
                        <button
                            class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary">
                            Top-up Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
