<x-guest-layout>
    <div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
            Buat Akun Baru
        </h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Bergabunglah dengan ribuan pengguna yang telah mempercayai kami
        </p>
    </div>

    <!-- Register Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Nama Lengkap
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="name" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name"
                       class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white/50 dark:bg-slate-700/50 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/20 dark:focus:ring-primary/20 dark:focus:border-primary/20 transition-colors"
                       placeholder="Masukkan nama lengkap Anda">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="username"
                       class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white/50 dark:bg-slate-700/50 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/20 dark:focus:ring-primary/20 dark:focus:border-primary/20 transition-colors"
                       placeholder="Masukkan email Anda">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white/50 dark:bg-slate-700/50 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/20 dark:focus:ring-primary/20 dark:focus:border-primary/20 transition-colors"
                       placeholder="Minimal 8 karakter">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Konfirmasi Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       class="block w-full pl-10 pr-3 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white/50 dark:bg-slate-700/50 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/20 dark:focus:ring-primary/20 dark:focus:border-primary/20 transition-colors"
                       placeholder="Ulangi password Anda">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms & Conditions -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="terms" 
                       type="checkbox" 
                       required
                       class="w-4 h-4 text-primary bg-slate-100 border-slate-300 rounded focus:ring-primary/20 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
            </div>
            <div class="ml-3 text-sm">
                <label for="terms" class="text-slate-600 dark:text-slate-400">
                    Saya setuju dengan 
                    <a href="#" class="text-primary hover:text-primary/80 transition-colors">Syarat & Ketentuan</a> 
                    dan 
                    <a href="#" class="text-primary hover:text-primary/80 transition-colors">Kebijakan Privasi</a>
                </label>
            </div>
        </div>

        <!-- Register Button -->
        <button type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-primary to-purple-600 hover:from-primary/90 hover:to-purple-600/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/20 transition-all duration-300 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Daftar Sekarang
        </button>
    </form>

    <!-- Divider -->
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400">Atau</span>
        </div>
    </div>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-sm text-slate-600 dark:text-slate-400">
            Sudah punya akun? 
            <a href="{{ route('login') }}" 
               class="font-medium text-primary hover:text-primary/80 transition-colors">
                Masuk di sini
            </a>
        </p>
    </div>

    <!-- Benefits -->
    <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 text-center">Mengapa memilih kami?</h3>
        <div class="grid grid-cols-1 gap-3">
            <div class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-xl">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Gratis Saldo Awal</p>
                    <p class="text-xs text-slate-600 dark:text-slate-400">Dapatkan Rp 5.000 untuk mencoba layanan</p>
                </div>
            </div>
            <div class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Proses Cepat</p>
                    <p class="text-xs text-slate-600 dark:text-slate-400">Order diproses dalam hitungan menit</p>
                </div>
            </div>
            <div class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Support 24/7</p>
                    <p class="text-xs text-slate-600 dark:text-slate-400">Tim support siap membantu Anda</p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
