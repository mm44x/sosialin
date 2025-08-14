<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit User #{{ $user->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-2xl bg-red-50 text-red-800 ring-1 ring-red-200">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input name="name" value="{{ old('name', $user->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border
                                   bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border
                                   bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Password baru (opsional)</label>
                            <input type="password" name="password"
                                class="mt-1 w-full px-3 py-2 rounded-xl border
                                       bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                placeholder="Biarkan kosong jika tidak diubah">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Konfirmasi password baru</label>
                            <input type="password" name="password_confirmation"
                                class="mt-1 w-full px-3 py-2 rounded-xl border
                                       bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div
                        class="flex items-start gap-3 p-3 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="mt-1"
                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <div>
                            <label for="is_active" class="font-medium">Aktifkan akun</label>
                            <p class="text-xs text-slate-500 dark:text-slate-300">
                                Hilangkan centang untuk menonaktifkan (banned). User nonaktif tidak dapat login /
                                bertransaksi.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.users.show', $user) }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                            Kembali
                        </a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <p class="mt-3 text-xs text-slate-500 dark:text-slate-300">
                * Perubahan email akan mempengaruhi kredensial login. Set password baru hanya jika diperlukan.
            </p>
        </div>
    </div>
</x-app-layout>
