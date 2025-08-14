<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit User #{{ $user->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input name="name" value="{{ old('name', $user->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Role</label>
                            @php $me = auth()->user(); @endphp
                            <select name="role"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                @disabled($user->id === $me->id)>
                                @foreach (['user' => 'User', 'admin' => 'Admin'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('role', $user->role) === $val)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($user->id === $me->id)
                                <p class="text-xs text-slate-500 mt-1">Tidak bisa mengubah role untuk akun Anda sendiri.
                                </p>
                            @endif
                        </div>

                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2">
                                @php $checked = old('is_active', (int)$user->is_active) ? true : false; @endphp
                                <input type="checkbox" name="is_active" value="1" class="rounded"
                                    @checked($checked) @disabled($user->id === $me->id)>
                                <span class="text-sm">Aktif (kosong = banned)</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Password baru (opsional)</label>
                            <input type="password" name="new_password"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Ulangi password baru</label>
                            <input type="password" name="new_password_confirm"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.users.show', $user) }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>

            <div class="mt-6 p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-red-200/60 dark:ring-red-400/30">
                <div class="font-semibold text-red-600 dark:text-red-400 mb-2">Danger Zone</div>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                    onsubmit="return confirm('Yakin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf @method('DELETE')
                    <button
                        class="px-4 py-2 rounded-xl border border-red-300 text-red-700 hover:bg-red-50 dark:text-red-300 dark:border-red-500">
                        Hapus User
                    </button>
                    <p class="text-xs text-slate-500 mt-2">Tidak bisa menghapus diri sendiri atau admin aktif terakhir.
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
