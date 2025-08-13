<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit Service #{{ $service->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Info dasar (internal/admin) --}}
                    <div>
                        <label class="block text-sm font-medium">Nama (internal)</label>
                        <input name="name" value="{{ old('name', $service->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Deskripsi (internal)</label>
                        <textarea name="description" rows="3"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Provider</label>
                            <select name="provider_id"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                                @foreach ($providers as $p)
                                    <option value="{{ $p->id }}" @selected(old('provider_id', $service->provider_id) == $p->id)>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provider_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Kategori</label>
                            <select name="category_id"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id', $service->category_id) == $c->id)>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">External Service ID (provider)</label>
                            <input name="external_service_id"
                                value="{{ old('external_service_id', $service->external_service_id) }}"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                required>
                            @error('external_service_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">
                                Markup Override (%) <span class="text-slateText dark:text-slate-300 text-xs">— kosongkan
                                    untuk pakai markup provider</span>
                            </label>
                            <input type="number" step="0.01" min="0" max="1000"
                                name="markup_percent_override"
                                value="{{ old('markup_percent_override', $service->markup_percent_override) }}"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            @error('markup_percent_override')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Kontrol visibilitas publik & copywriting publik --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <div class="flex items-center gap-2">
                                {{-- hidden agar saat unchecked tetap terkirim 0 --}}
                                <input type="hidden" name="public_active" value="0">
                                <input id="public_active" name="public_active" type="checkbox" value="1"
                                    @checked(old('public_active', $service->public_active))>
                                <label for="public_active" class="text-sm">Tampilkan di Halaman Publik
                                    (/services)</label>
                            </div>
                            @error('public_active')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium">Nama Publik (opsional)</label>
                            <input name="public_name" value="{{ old('public_name', $service->public_name) }}"
                                placeholder="Nama yang tampil ke pelanggan (jika kosong, pakai Nama internal)"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            @error('public_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium">Deskripsi Publik (opsional)</label>
                            <textarea name="public_description" rows="3" placeholder="Deskripsi ringkas untuk halaman publik"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">{{ old('public_description', $service->public_description) }}</textarea>
                            @error('public_description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status aktif internal --}}
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="active" value="0">
                        <input id="active" name="active" type="checkbox" value="1"
                            @checked(old('active', $service->active))>
                        <label for="active" class="text-sm">Active (internal)</label>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.services.index') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
