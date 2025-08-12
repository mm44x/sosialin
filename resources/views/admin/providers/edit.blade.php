<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Admin — Edit Provider: {{ $provider->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.providers.update', $provider) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Base URL</label>
                        <input value="{{ $provider->base_url }}" disabled
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-gray-100 dark:bg-gray-800 dark:border-gray-600">
                        <p class="text-xs text-slateText dark:text-slate-400 mt-1">Diambil dari ENV/seed — ubah nanti
                            jika perlu.</p>
                    </div>

                    <div>
                        <label for="markup_percent" class="block text-sm font-medium">Markup % (semua layanan
                            provider)</label>
                        <input id="markup_percent" name="markup_percent" type="number" step="0.01" min="0"
                            max="1000" value="{{ old('markup_percent', $provider->markup_percent) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('markup_percent')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="active" name="active" type="checkbox" value="1"
                            {{ $provider->active ? 'checked' : '' }}>
                        <label for="active" class="text-sm">Active</label>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.providers.index') }}"
                            class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
