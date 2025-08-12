<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit Category</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input name="name" value="{{ old('name', $category->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Provider (opsional)</label>
                        <select name="provider_id"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <option value="">— None —</option>
                            @foreach ($providers as $p)
                                <option value="{{ $p->id }}" @selected(old('provider_id', $category->provider_id) == $p->id)>{{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input id="active" name="active" type="checkbox" value="1"
                            {{ $category->active ? 'checked' : '' }}>
                        <label for="active" class="text-sm">Active</label>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.categories.index') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
