<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Buat Tiket Bantuan
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Buat tiket bantuan baru untuk mendapatkan dukungan dari tim kami
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('tickets.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Daftar Tiket
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Welcome Banner --}}
            <div
                class="p-6 rounded-2xl bg-gradient-to-r from-primary/10 to-purple-600/10 border border-primary/20 dark:border-primary/30">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-primary/20 text-primary">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Buat Tiket Bantuan Baru</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Jelaskan kendala Anda dengan detail agar tim kami dapat membantu dengan lebih baik
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Card --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Form Tiket Bantuan</h3>
                </div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    {{-- Subject Field --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2" for="subject">
                            Subjek Tiket
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <input id="subject" name="subject" value="{{ old('subject') }}" required
                                class="pl-10 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                          bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                          focus:border-primary/20 dark:focus:border-primary/20
                                          transition-colors"
                                placeholder="Contoh: Masalah dengan order #1234">
                        </div>
                        @error('subject')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Order ID Field --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2" for="order_id">
                            Order ID (Opsional)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <input type="number" id="order_id" name="order_id" value="{{ old('order_id') }}"
                                class="pl-10 w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                          bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                          focus:border-primary/20 dark:focus:border-primary/20
                                          transition-colors"
                                placeholder="Contoh: 1234">
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                            Isi Order ID jika tiket terkait dengan pesanan tertentu
                        </p>
                        @error('order_id')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message Field --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2" for="message">
                            Detail Keluhan
                        </label>
                        <div class="relative">
                            <textarea id="message" name="message" rows="6" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                             bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                             placeholder-slate-400 dark:placeholder-slate-500
                                             focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                             focus:border-primary/20 dark:focus:border-primary/20
                                             transition-colors resize-none"
                                placeholder="Ceritakan kendala yang Anda alami secara detail...">{{ old('message') }}</textarea>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                            Semakin detail penjelasan, semakin cepat kami dapat membantu
                        </p>
                        @error('message')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Attachment Field --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"
                            for="attachment">
                            Lampiran (Opsional)
                        </label>
                        <div id="dropZone"
                            class="mt-2 flex justify-center px-6 pt-6 pb-6 border-2 border-dashed rounded-xl
                                    border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 hover:border-primary/50 dark:hover:border-primary/50 transition-colors"
                            data-drag-over="border-primary dark:border-primary bg-primary/5 dark:bg-primary/5">
                            <div class="space-y-3 text-center">
                                <div id="uploadIcon" class="space-y-2">
                                    <svg class="mx-auto h-16 w-16 text-slate-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        </path>
                                    </svg>
                                    <div class="text-sm text-slate-500 dark:text-slate-400">
                                        <span class="font-medium text-primary">Upload file</span> atau drag & drop
                                    </div>
                                </div>

                                <div id="filePreview" class="hidden space-y-2">
                                    <div class="flex items-center justify-center">
                                        <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm text-slate-600 dark:text-slate-300">
                                        <span id="fileName" class="font-medium"></span>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        File berhasil dipilih
                                    </div>
                                </div>

                                <div class="flex justify-center">
                                    <label
                                        class="relative cursor-pointer rounded-lg font-medium text-primary hover:opacity-75 transition-opacity">
                                        <span
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-primary/20 bg-primary/5 hover:bg-primary/10">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            Pilih File
                                        </span>
                                        <input type="file" id="attachment" name="attachment" class="sr-only"
                                            accept=".jpg,.jpeg,.png,.webp,.pdf">
                                    </label>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    JPG, PNG, WEBP, atau PDF maksimal 5MB
                                </p>
                            </div>
                        </div>
                        @error('attachment')
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4">
                        <a href="{{ route('tickets.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Kembali ke Daftar Tiket</span>
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-purple-600 text-white font-medium shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Kirim Tiket</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Error Display --}}
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700/50">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 p-2 rounded-lg bg-red-100 dark:bg-red-800/50">
                            <svg class="h-5 w-5 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300 mb-2">Terjadi Kesalahan</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-200 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dropZone = document.getElementById('dropZone');
                const fileInput = document.getElementById('attachment');
                const uploadIcon = document.getElementById('uploadIcon');
                const filePreview = document.getElementById('filePreview');
                const fileName = document.getElementById('fileName');

                // Prevent default drag behaviors
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                // Highlight drop zone when item is dragged over it
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });

                // Handle dropped files
                dropZone.addEventListener('drop', handleDrop, false);

                // Handle file input change
                fileInput.addEventListener('change', handleFileSelect);

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                function highlight(e) {
                    const dragOverClasses = dropZone.getAttribute('data-drag-over').split(' ');
                    dropZone.classList.add(...dragOverClasses);
                }

                function unhighlight(e) {
                    const dragOverClasses = dropZone.getAttribute('data-drag-over').split(' ');
                    dropZone.classList.remove(...dragOverClasses);
                }

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    handleFiles(files);
                }

                function handleFileSelect(e) {
                    const files = e.target.files;
                    handleFiles(files);
                }

                function handleFiles(files) {
                    if (files.length > 0) {
                        const file = files[0];

                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'];
                        if (!allowedTypes.includes(file.type)) {
                            showError('Tipe file tidak didukung. Gunakan JPG, PNG, WEBP, atau PDF.');
                            return;
                        }

                        // Validate file size (5MB)
                        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                        if (file.size > maxSize) {
                            showError('Ukuran file terlalu besar. Maksimal 5MB.');
                            return;
                        }

                        // Show file preview
                        showFilePreview(file);

                        // Update file input
                        fileInput.files = files;
                    }
                }

                function showFilePreview(file) {
                    // Hide upload icon and show file preview
                    uploadIcon.classList.add('hidden');
                    filePreview.classList.remove('hidden');

                    // Update file name
                    fileName.textContent = file.name;

                    // Add success styling to drop zone
                    dropZone.classList.add('border-green-300', 'dark:border-green-600', 'bg-green-50',
                        'dark:bg-green-900/20');
                }

                function showError(message) {
                    // Create error notification
                    const errorDiv = document.createElement('div');
                    errorDiv.className =
                    'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    errorDiv.textContent = message;

                    document.body.appendChild(errorDiv);

                    // Remove after 3 seconds
                    setTimeout(() => {
                        if (errorDiv.parentNode) {
                            errorDiv.parentNode.removeChild(errorDiv);
                        }
                    }, 3000);
                }

                // Add click to upload functionality
                dropZone.addEventListener('click', function(e) {
                    // Don't trigger if clicking on the file input label
                    if (e.target.closest('label')) {
                        return;
                    }
                    fileInput.click();
                });

                // Add hover effect for better UX
                dropZone.addEventListener('mouseenter', function() {
                    if (!filePreview.classList.contains('hidden')) return;
                    dropZone.classList.add('border-primary/50', 'dark:border-primary/50', 'bg-primary/5',
                        'dark:bg-primary/5');
                });

                dropZone.addEventListener('mouseleave', function() {
                    if (!filePreview.classList.contains('hidden')) return;
                    dropZone.classList.remove('border-primary/50', 'dark:border-primary/50', 'bg-primary/5',
                        'dark:bg-primary/5');
                });
            });
        </script>
    @endpush
</x-app-layout>
