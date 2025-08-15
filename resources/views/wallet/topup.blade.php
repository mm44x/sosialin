<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Top Up Saldo</h2>
    </x-slot>

    <div class="py-6" x-data="topupForm">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Status Message --}}
            @if (session('status'))
                <div
                    class="p-4 rounded-2xl {{ session('status.type') === 'error' ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400' }} ring-1 ring-current/10">
                    {{ session('status.message') ?? session('status') }}
                </div>
            @endif

            {{-- Saldo Info --}}
            <div class="p-4 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-600 dark:text-slate-300">Saldo Anda</div>
                    <div class="text-lg font-semibold">Rp {{ number_format($balance ?? 0, 2) }}</div>
                </div>
            </div>

            {{-- Daftar Metode Pembayaran --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-semibold">Pilih Metode Pembayaran</div>
                    <div class="text-xs text-slate-500">* Klik metode untuk memilih</div>
                </div>

                @if (($methods ?? collect())->isEmpty())
                    <div class="text-sm text-slate-600 dark:text-slate-300">
                        Belum ada metode pembayaran aktif. Silakan hubungi admin.
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach ($methods as $m)
                            <li @click="selectMethod('{{ $m->id }}')"
                                class="p-4 rounded-xl ring-1 transition-all cursor-pointer"
                                :class="selectedMethod === '{{ $m->id }}' ?
                                    'ring-primary dark:ring-primary bg-primary/5 dark:bg-primary/5' :
                                    'ring-slate-200/60 dark:ring-white/10 bg-white/70 dark:bg-white/5 hover:ring-primary/50 dark:hover:ring-primary/50'">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="font-medium">{{ $m->name }}</div>
                                            @if ($m->type === 'bank')
                                                <span
                                                    class="px-2 py-0.5 text-xs rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Bank</span>
                                            @elseif($m->type === 'ewallet')
                                                <span
                                                    class="px-2 py-0.5 text-xs rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">E-Wallet</span>
                                            @endif
                                        </div>
                                        @if ($m->type === 'bank')
                                            <div
                                                class="text-sm text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                                <span>{{ $m->bank_name }} • {{ $m->account_number }}</span>
                                                <button type="button" class="text-primary hover:opacity-75"
                                                    @click.stop="navigator.clipboard.writeText('{{ $m->account_number }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="text-sm text-slate-600 dark:text-slate-300">
                                                a.n {{ $m->account_name }}
                                            </div>
                                        @endif
                                        @if ($m->instructions)
                                            <div
                                                class="mt-2 text-xs text-slate-500 dark:text-slate-400 prose prose-sm max-w-none dark:prose-invert">
                                                {!! $m->instructions !!}
                                            </div>
                                        @endif
                                    </div>
                                    @if ($m->media_path)
                                        <div class="shrink-0">
                                            <a href="{{ asset('storage/' . $m->media_path) }}" target="_blank"
                                                @click.stop
                                                class="inline-flex items-center gap-1 text-sm text-primary hover:opacity-75">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                Lihat QR Code
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
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('wallet.topup.store') }}" enctype="multipart/form-data"
                    class="space-y-6" @submit.prevent="submitForm" x-ref="form">
                    @csrf

                    <input type="hidden" name="payment_method_id" :value="selectedMethod" required>

                    <div>
                        <label class="block text-sm font-medium mb-1">Nominal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500">Rp</span>
                            </div>
                            <input type="number" name="amount" x-model="amount" min="{{ $minAmount ?? 1000 }}"
                                max="{{ $maxAmount ?? 100000000 }}" step="1000" required
                                class="pl-8 mt-1 w-full px-3 py-2 rounded-xl border
                                          bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600
                                          focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Contoh: 50000">
                        </div>
                        <div class="mt-1.5 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Min: Rp {{ number_format($minAmount ?? 1000) }}</span>
                            <span class="text-slate-500">Max: Rp {{ number_format($maxAmount ?? 100000000) }}</span>
                        </div>
                        @error('amount')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Bukti Transfer</label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-xl
                                    border-slate-300 dark:border-slate-600">
                            <div class="space-y-2 text-center">
                                <template x-if="!previewImage">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </template>
                                <template x-if="previewImage">
                                    <img :src="previewImage" class="mx-auto h-32 w-auto rounded-lg">
                                </template>
                                <div class="flex justify-center text-sm">
                                    <label
                                        class="relative cursor-pointer rounded-md font-medium text-primary hover:opacity-75">
                                        <span>Upload file</span>
                                        <input type="file" name="proof" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                            required @change="handleFileChange" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-slate-500">
                                    JPG, PNG, WEBP, atau PDF maksimal 5MB
                                </p>
                            </div>
                        </div>
                        @error('proof')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                        <textarea name="note" rows="3"
                            class="mt-1 w-full px-3 py-2 rounded-xl border
                                         bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600
                                         focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="Tambahkan keterangan jika perlu."></textarea>
                        @error('note')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-slate-500" x-show="amount">
                                Total: Rp <span x-text="new Intl.NumberFormat().format(amount)"></span>
                            </span>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 disabled:opacity-50"
                                :disabled="isSubmitting">
                                <template x-if="!isSubmitting">
                                    <span>Kirim Permintaan</span>
                                </template>
                                <template x-if="isSubmitting">
                                    <div class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
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

            {{-- Info Panel --}}
            <div class="rounded-2xl bg-blue-50 dark:bg-blue-900/30 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Informasi Penting</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-200 space-y-1">
                            <p>• Top up akan diproses manual oleh admin setelah bukti transfer diverifikasi.</p>
                            <p>• Pastikan nominal transfer sesuai dengan yang diinputkan.</p>
                            <p>• Waktu pemrosesan 1-24 jam pada hari kerja.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
