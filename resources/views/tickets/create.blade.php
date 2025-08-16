<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Buat Tiket Bantuan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium">Subjek</label>
                        <input name="subject" value="{{ old('subject') }}" required
                               class="mt-1 w-full px-3 py-2 rounded-xl border
                                      bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Order ID (opsional)</label>
                        <input type="number" name="order_id" value="{{ old('order_id') }}"
                               class="mt-1 w-full px-3 py-2 rounded-xl border
                                      bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                               placeholder="contoh: 1234">
                        <p class="text-xs text-slate-500 mt-1">Isi sendiri agar tidak membingungkan.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Detail Keluhan</label>
                        <textarea name="message" rows="5" required
                                  class="mt-1 w-full px-3 py-2 rounded-xl border
                                         bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                                  placeholder="Ceritakan kendala yang Anda alami...">{{ old('message') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Lampiran (opsional)</label>
                        <input type="file" name="attachment"
                               class="mt-1 block w-full text-sm file:mr-3 file:px-3 file:py-2 file:rounded-xl
                                      file:border file:bg-white dark:file:bg-slate-800 dark:file:text-white
                                      file:border-slate-300 dark:file:border-slate-600
                                      border rounded-xl dark:bg-slate-800 dark:text-white dark:border-slate-600" />
                        <p class="text-xs text-slate-500 mt-1">jpg, png, webp, pdf (≤ 5MB)</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('tickets.index') }}"
                           class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Kirim Tiket</button>
                    </div>
                </form>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-3 rounded-2xl bg-red-50 text-red-800 ring-1 ring-red-200">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
