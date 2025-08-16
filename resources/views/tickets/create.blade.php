<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Buat Tiket Bantuan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-2xl bg-red-50 text-red-800 ring-1 ring-red-200 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Subject</label>
                        <input name="subject" value="{{ old('subject') }}" required
                               class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Order ID (opsional)</label>
                        <input type="number" name="order_id" value="{{ old('order_id') }}"
                               class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                               placeholder="mis. 1024">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">
                            Isi ID order jika keluhan terkait pesanan tertentu. Kosongkan jika tidak ada.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Detail Keluhan</label>
                        <textarea name="message" rows="6" required
                                  class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                  placeholder="Jelaskan kendala Anda...">{{ old('message') }}</textarea>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('tickets.index') }}"
                           class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Kirim</button>
                    </div>
                </form>
            </div>

            <p class="mt-4 text-xs text-slate-500 dark:text-slate-300">
                Tim kami akan membalas di halaman detail tiket.
            </p>
        </div>
    </div>
</x-app-layout>