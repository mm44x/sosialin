<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Edit Service #{{ $service->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Edit informasi dan pengaturan layanan
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('admin.services.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Daftar Services
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Edit Service Form --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Edit Informasi Service</h3>
                </div>

                <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Info Dasar (Internal/Admin) --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Informasi Internal</span>
                            <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="name">
                                    Nama (Internal)
                                </label>
                                <input name="name" id="name" value="{{ old('name', $service->name) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors"
                                    placeholder="Nama internal layanan" required>
                                @error('name')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="description">
                                    Deskripsi (Internal)
                                </label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors resize-none"
                                    placeholder="Deskripsi internal layanan">{{ old('description', $service->description) }}</textarea>
                                @error('description')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="provider_id">
                                    Provider
                                </label>
                                <select name="provider_id" id="provider_id"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors">
                                    @foreach ($providers as $p)
                                        <option value="{{ $p->id }}" @selected(old('provider_id', $service->provider_id) == $p->id)>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provider_id')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="category_id">
                                    Kategori
                                </label>
                                <select name="category_id" id="category_id"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors">
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}" @selected(old('category_id', $service->category_id) == $c->id)>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="external_service_id">
                                    External Service ID (Provider)
                                </label>
                                <input name="external_service_id" id="external_service_id"
                                    value="{{ old('external_service_id', $service->external_service_id) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors"
                                    placeholder="ID layanan dari provider" required>
                                @error('external_service_id')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="markup_percent_override">
                                    Markup Override (%)
                                </label>
                                <input type="number" step="0.01" min="0" max="1000" id="markup_percent_override"
                                    name="markup_percent_override"
                                    value="{{ old('markup_percent_override', $service->markup_percent_override) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors"
                                    placeholder="Kosongkan untuk pakai markup provider">
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Kosongkan untuk menggunakan markup dari provider
                                </p>
                                @error('markup_percent_override')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Kontrol Visibilitas Publik & Copywriting Publik --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Pengaturan Publik</span>
                            <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                <input type="hidden" name="public_active" value="0">
                                <input id="public_active" name="public_active" type="checkbox" value="1"
                                    class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20"
                                    @checked(old('public_active', $service->public_active))>
                                <label for="public_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Tampilkan di Halaman Publik (/services)
                                </label>
                            </div>
                            @error('public_active')
                                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="public_name">
                                    Nama Publik (Opsional)
                                </label>
                                <input name="public_name" id="public_name" value="{{ old('public_name', $service->public_name) }}"
                                    placeholder="Nama yang tampil ke pelanggan (jika kosong, pakai nama internal)"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors">
                                @error('public_name')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="public_description">
                                    Deskripsi Publik (Opsional)
                                </label>
                                <textarea name="public_description" id="public_description" rows="3" 
                                    placeholder="Deskripsi ringkas untuk halaman publik"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors resize-none">{{ old('public_description', $service->public_description) }}</textarea>
                                @error('public_description')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Status Aktif Internal --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Status Internal</span>
                            <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                        </div>

                        <div class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <input type="hidden" name="active" value="0">
                            <input id="active" name="active" type="checkbox" value="1"
                                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20"
                                @checked(old('active', $service->active))>
                            <label for="active" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Active (Internal)
                            </label>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('admin.services.index') }}"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                   border border-slate-200 dark:border-slate-700
                                   hover:border-primary/20 dark:hover:border-primary/20 
                                   hover:bg-primary/5 dark:hover:bg-primary/5
                                   text-slate-700 dark:text-slate-300
                                   transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Kembali</span>
                        </a>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                       bg-gradient-to-r from-primary to-purple-600 
                                       text-white font-medium shadow-sm 
                                       hover:shadow-md transition-all duration-300 hover:scale-105
                                       focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
