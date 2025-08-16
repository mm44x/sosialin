<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Top Up Saldo
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Tambahkan saldo ke wallet Anda untuk memulai order layanan
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('dashboard') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Dashboard
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6" x-data="topupForm">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alpine JS Data --}}
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('topupForm', () => ({
                        selectedMethod: '',
                        amount: '',
                        isSubmitting: false,
                        previewImage: null,

                        init() {
                            @if (old('payment_method_id'))
                                this.selectedMethod = '{{ old('payment_method_id') }}';
                            @endif
                            @if (old('amount'))
                                this.amount = '{{ old('amount') }}';
                            @endif
                        },

                        selectMethod(id) {
                            this.selectedMethod = id;
                        },

                        handleFileChange(event) {
                            const file = event.target.files[0];
                            if (file && file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = (e) => this.previewImage = e.target.result;
                                reader.readAsDataURL(file);
                            } else {
                                this.previewImage = null;
                            }
                        },

                        submitForm() {
                            if (!this.selectedMethod) {
                                alert('Silakan pilih metode pembayaran terlebih dahulu');
                                return;
                            }
                            this.isSubmitting = true;
                            this.$refs.form.submit();
                        }
                    }))
                })
            </script>

            {{-- Welcome Banner --}}
            <div
                class="p-6 rounded-2xl bg-gradient-to-r from-primary/10 to-purple-600/10 border border-primary/20 dark:border-primary/30">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-primary/20 text-primary">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Top Up Saldo Wallet</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Pilih metode pembayaran yang tersedia dan ikuti instruksi untuk menambah saldo
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Daftar Metode Pembayaran --}}
                    <div
                        class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pilih Metode Pembayaran
                                </h3>
                            </div>
                            <div
                                class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-lg">
                                * Klik metode untuk memilih</div>
                        </div>

                        @if (($methods ?? collect())->isEmpty())
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada metode
                                    pembayaran</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Silakan hubungi admin untuk
                                    mengaktifkan metode pembayaran.</p>
                            </div>
                        @else
                            <ul class="space-y-4">
                                @foreach ($methods as $m)
                                    <li @click="selectMethod('{{ $m->id }}')"
                                        class="p-5 rounded-xl ring-1 transition-all cursor-pointer group"
                                        :class="selectedMethod === '{{ $m->id }}' ?
                                            'ring-primary dark:ring-primary bg-primary/5 dark:bg-primary/5' :
                                            'ring-slate-200/60 dark:ring-slate-700/60 bg-white/50 dark:bg-slate-800/50 hover:ring-primary/50 dark:hover:ring-primary/50 hover:bg-primary/5 dark:hover:bg-primary/5'">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                                        @if ($m->type === 'bank')
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                            </svg>
                                                        @elseif($m->type === 'ewallet')
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-slate-900 dark:text-white">
                                                            {{ $m->name }}</div>
                                                        @if ($m->type === 'bank')
                                                            <span
                                                                class="inline-block px-2 py-1 text-xs rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Bank</span>
                                                        @elseif($m->type === 'ewallet')
                                                            <span
                                                                class="inline-block px-2 py-1 text-xs rounded-lg bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">E-Wallet</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if ($m->type === 'bank')
                                                    <div class="space-y-2">
                                                        <div
                                                            class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                                            <span class="font-medium">{{ $m->bank_name }}</span>
                                                            <span class="text-slate-400">•</span>
                                                            <span class="font-mono">{{ $m->account_number }}</span>
                                                            <button type="button"
                                                                class="text-primary hover:opacity-75 transition-opacity"
                                                                @click.stop="navigator.clipboard.writeText('{{ $m->account_number }}')"
                                                                title="Salin nomor rekening">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="text-sm text-slate-600 dark:text-slate-300">
                                                            a.n <span class="font-medium">{{ $m->account_name }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($m->instructions)
                                                    <div class="mt-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                                        <div
                                                            class="text-xs text-slate-500 dark:text-slate-400 prose prose-sm max-w-none dark:prose-invert">
                                                            {!! $m->instructions !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($m->media_path)
                                                <div class="shrink-0">
                                                    <a href="{{ asset('storage/' . $m->media_path) }}"
                                                        target="_blank" @click.stop
                                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a3 3 0 00-3 3v12a3 3 0 003 3z" />
                                                        </svg>
                                                        QR Code
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Form request topup --}}
                    <div
                        class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Form Top Up Saldo</h3>
                        </div>

                        <form method="POST" action="{{ route('wallet.topup.store') }}"
                            enctype="multipart/form-data" class="space-y-6" @submit.prevent="submitForm"
                            x-ref="form">
                            @csrf

                            <input type="hidden" name="payment_method_id" :value="selectedMethod" required>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nominal
                                    Top Up</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-500 font-medium">Rp</span>
                                    </div>
                                    <input type="number" name="amount" x-model="amount"
                                        min="{{ $minAmount ?? 1000 }}" max="{{ $maxAmount ?? 100000000 }}"
                                        step="1000" required
                                        class="pl-12 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                          bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                          focus:border-primary/20 dark:focus:border-primary/20
                                          transition-colors"
                                        placeholder="Contoh: 50000">
                                </div>
                                <div
                                    class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                    <span>Min: Rp {{ number_format($minAmount ?? 1000) }}</span>
                                    <span>Max: Rp {{ number_format($maxAmount ?? 100000000) }}</span>
                                </div>
                                @error('amount')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bukti
                                    Transfer</label>
                                <div
                                    class="mt-2 flex justify-center px-6 pt-6 pb-6 border-2 border-dashed rounded-xl
                                    border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 hover:border-primary/50 dark:hover:border-primary/50 transition-colors">
                                    <div class="space-y-3 text-center">
                                        <template x-if="!previewImage">
                                            <div class="space-y-2">
                                                <svg class="mx-auto h-16 w-16 text-slate-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                                    <span class="font-medium text-primary">Upload file</span> atau drag
                                                    & drop
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="previewImage">
                                            <div class="space-y-2">
                                                <img :src="previewImage"
                                                    class="mx-auto h-32 w-auto rounded-lg border border-slate-200 dark:border-slate-700">
                                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                                    File berhasil dipilih
                                                </div>
                                            </div>
                                        </template>
                                        <div class="flex justify-center">
                                            <label
                                                class="relative cursor-pointer rounded-lg font-medium text-primary hover:opacity-75 transition-opacity">
                                                <span
                                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-primary/20 bg-primary/5 hover:bg-primary/10">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    Pilih File
                                                </span>
                                                <input type="file" name="proof"
                                                    accept=".jpg,.jpeg,.png,.webp,.pdf" required
                                                    @change="handleFileChange" class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            JPG, PNG, WEBP, atau PDF maksimal 5MB
                                        </p>
                                    </div>
                                </div>
                                @error('proof')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Catatan
                                    (opsional)</label>
                                <textarea name="note" rows="3"
                                    class="mt-2 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                         bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                         placeholder-slate-400 dark:placeholder-slate-500
                                         focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                         focus:border-primary/20 dark:focus:border-primary/20
                                         transition-colors"
                                    placeholder="Tambahkan keterangan jika perlu."></textarea>
                                @error('note')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4">
                                <a href="{{ route('dashboard') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali ke Dashboard
                                </a>
                                <div class="flex flex-col sm:flex-row items-center gap-3">
                                    <div class="text-center sm:text-right">
                                        <span class="text-sm text-slate-500 dark:text-slate-400" x-show="amount">
                                            Total: <span class="font-semibold text-slate-900 dark:text-white">Rp <span
                                                    x-text="new Intl.NumberFormat('id-ID').format(amount)"></span></span>
                                        </span>
                                    </div>
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-purple-600 text-white font-medium shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100"
                                        :disabled="isSubmitting">
                                        <template x-if="!isSubmitting">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span>Kirim Permintaan Top Up</span>
                                            </div>
                                        </template>
                                        <template x-if="isSubmitting">
                                            <div class="flex items-center gap-2">
                                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4">
                                                    </circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                <span>Mengirim...</span>
                                            </div>
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">
                    {{-- Info Panel --}}
                    <div
                        class="p-6 rounded-2xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700/50">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 p-2 rounded-lg bg-blue-100 dark:bg-blue-800/50">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3">Informasi
                                    Penting</h3>
                                <div class="space-y-2 text-sm text-blue-700 dark:text-blue-200">
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-2 flex-shrink-0"></div>
                                        <p>Top up akan diproses manual oleh admin setelah bukti transfer diverifikasi.
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-2 flex-shrink-0"></div>
                                        <p>Pastikan nominal transfer sesuai dengan yang diinputkan.</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-2 flex-shrink-0"></div>
                                        <p>Waktu pemrosesan 1-24 jam pada hari kerja.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Permintaan Top Up --}}
                    <div
                        class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                        <div class="p-4 border-b border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-slate-900 dark:text-white">Riwayat Permintaan Top Up</h3>
                            </div>
                        </div>

                        @if ($topups->isEmpty())
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada riwayat
                                </h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Riwayat permintaan top up Anda
                                    akan muncul di sini</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                                        <tr class="text-left">
                                            <th
                                                class="px-4 py-3 text-sm font-medium text-slate-500 dark:text-slate-400">
                                                Ref</th>
                                            <th
                                                class="px-4 py-3 text-sm font-medium text-slate-500 dark:text-slate-400">
                                                Tanggal</th>
                                            <th
                                                class="px-4 py-3 text-sm font-medium text-sm font-medium text-slate-500 dark:text-slate-400">
                                                Metode</th>
                                            <th
                                                class="px-4 py-3 text-sm font-medium text-slate-500 dark:text-slate-400">
                                                Nominal</th>
                                            <th
                                                class="px-4 py-3 text-sm font-medium text-slate-500 dark:text-slate-400">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/60 dark:divide-slate-700/60">
                                        @foreach ($topups as $topup)
                                            <tr
                                                class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="font-mono text-sm text-slate-900 dark:text-white">{{ $topup->reference }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300">
                                                    {{ $topup->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        @if ($topup->method === 'bank')
                                                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                                        @elseif($topup->method === 'ewallet')
                                                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                                        @else
                                                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                                        @endif
                                                        <span class="text-sm text-slate-600 dark:text-slate-300">
                                                            {{ $topup->meta['method_name'] ?? $topup->method }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="font-medium text-slate-900 dark:text-white">
                                                        Rp {{ number_format($topup->amount, 0) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($topup->status === 'pending')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-800 ring-1 ring-inset ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30">
                                                            Menunggu
                                                        </span>
                                                    @elseif($topup->status === 'approved')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 ring-1 ring-inset ring-green-200 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-400/30">
                                                            Disetujui
                                                        </span>
                                                    @elseif($topup->status === 'rejected')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-800 ring-1 ring-inset ring-red-200 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-400/30">
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
