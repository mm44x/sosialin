<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit Category #{{ $category->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div class="text-sm text-slateText dark:text-slate-300">
                        Provider: <span class="font-medium">{{ $category->provider->name ?? '—' }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input name="name" value="{{ old('name', $category->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="active" value="0">
                        <input id="active" name="active" type="checkbox" value="1"
                            @checked(old('active', $category->active))>
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
