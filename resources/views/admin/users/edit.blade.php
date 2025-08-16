<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Edit Pengguna #{{ $user->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Edit informasi dan pengaturan pengguna
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('admin.users.show', $user) }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Detail Pengguna
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Edit User Form --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Edit Informasi Pengguna</h3>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf @method('PUT')

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="name">
                                Nama Lengkap
                            </label>
                            <input name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">
                                Email
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                placeholder="Masukkan email" required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="role">
                                Role
                            </label>
                            @php $me = auth()->user(); @endphp
                            <select name="role" id="role"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors"
                                @disabled($user->id === $me->id)>
                                @foreach (['user' => 'User', 'admin' => 'Admin'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('role', $user->role) === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if ($user->id === $me->id)
                                <div class="flex items-center gap-2 mt-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-xs text-amber-800 dark:text-amber-200">
                                        Tidak bisa mengubah role untuk akun Anda sendiri
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Status Akun
                            </label>
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                @php $checked = old('is_active', (int)$user->is_active) ? true : false; @endphp
                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" 
                                           class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20"
                                           @checked($checked) @disabled($user->id === $me->id)>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ $checked ? 'Aktif' : 'Banned' }}
                                    </span>
                                </label>
                                @if ($user->id === $me->id)
                                    <div class="flex items-center gap-2 p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span class="text-xs text-amber-800 dark:text-amber-200">Tidak bisa diubah</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Password Baru (Opsional)</span>
                            <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="new_password">
                                    Password Baru
                                </label>
                                <input type="password" name="new_password" id="new_password"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors"
                                    placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="new_password_confirm">
                                    Konfirmasi Password
                                </label>
                                <input type="password" name="new_password_confirm" id="new_password_confirm"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors"
                                    placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('admin.users.show', $user) }}"
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

            {{-- Danger Zone --}}
            <div class="p-6 rounded-2xl bg-red-50 dark:bg-red-900/20 ring-1 ring-red-200/60 dark:ring-red-400/30">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-lg bg-red-100 dark:bg-red-800/50 text-red-600 dark:text-red-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Danger Zone</h3>
                </div>

                <div class="space-y-4">
                    <p class="text-sm text-red-700 dark:text-red-300">
                        Tindakan ini tidak dapat dibatalkan. User yang dihapus akan kehilangan akses ke sistem secara permanen.
                    </p>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                        onsubmit="return confirm('Yakin menghapus user ini? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                       border border-red-300 dark:border-red-500
                                       bg-red-100 dark:bg-red-800/50
                                       text-red-700 dark:text-red-300
                                       hover:bg-red-200 dark:hover:bg-red-700/50
                                       transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus User</span>
                        </button>
                    </form>

                    <div class="flex items-start gap-3 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50">
                        <div class="flex-shrink-0 p-1 rounded-lg bg-amber-100 dark:bg-amber-800/50">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="text-sm text-amber-800 dark:text-amber-200">
                            <strong>Perhatian:</strong> Tidak bisa menghapus diri sendiri atau admin aktif terakhir. Pastikan ada minimal satu admin aktif sebelum menghapus user lain.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
