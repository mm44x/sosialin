<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit Service #{{ $service->id }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input name="name" value="{{ old('name', $service->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Provider</label>
                            <select name="provider_id"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                                @foreach ($providers as $p)
                                    <option value="{{ $p->id }}" @selected(old('provider_id', $service->provider_id) == $p->id)>{{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Kategori</label>
                            <select name="category_id"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id', $service->category_id) == $c->id)>{{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">External Service ID (provider)</label>
                            <input name="external_service_id"
                                value="{{ old('external_service_id', $service->external_service_id) }}"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Markup Override (%) — kosongkan untuk pakai markup
                                provider</label>
                            <input type="number" step="0.01" min="0" max="1000"
                                name="markup_percent_override"
                                value="{{ old('markup_percent_override', $service->markup_percent_override) }}"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="active" name="active" type="checkbox" value="1"
                            {{ $service->active ? 'checked' : '' }}>
                        <label for="active" class="text-sm">Active</label>
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
