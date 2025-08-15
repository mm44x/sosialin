@extends('layouts.marketing')

@section('content')
    {{-- Hero --}}
    <section class="bg-surface-light dark:bg-surface-dark text-slate-900 dark:text-slate-100">
        <div class="container mx-auto px-4 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <div class="space-y-6">
                    <span class="inline-block px-4 py-2 rounded-full bg-primary/10 text-primary font-medium text-sm">
                        Platform SMM Panel #1 di Indonesia
                    </span>

                    <h1
                        class="text-4xl md:text-6xl font-bold leading-tight bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                        Tingkatkan Presence<br>
                        Social Media Anda
                    </h1>

                    <p class="text-lg text-slate-600 dark:text-slate-300 max-w-xl">
                        Penyedia layanan SMM Panel terpercaya dengan harga termurah dan kualitas terbaik.
                        Tingkatkan followers, likes, views & engagement Anda secara instan.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}"
                            class="group relative px-6 py-3 rounded-xl bg-primary text-white font-medium shadow-soft 
                                   overflow-hidden transition-all duration-300 hover:shadow-lg hover:scale-105">
                            <span class="relative z-10">Mulai Sekarang</span>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-primary to-purple-600 opacity-0 
                                      group-hover:opacity-100 transition-opacity">
                            </div>
                        </a>
                        <a href="{{ route('services.index') }}"
                            class="group px-6 py-3 rounded-xl border-2 border-primary/20 text-primary dark:text-white
                                   font-medium hover:bg-primary/10 transition-all duration-300 hover:scale-105">
                            Lihat Layanan
                            <span class="inline-block transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-8 pt-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm">Proses Instan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="text-sm">100% Aman</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm">Harga Termurah</span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative bg-white/60 dark:bg-white/5 rounded-3xl p-8 shadow-lg backdrop-blur-sm 
                         ring-1 ring-slate-200/60 dark:ring-white/10 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-purple-500/5"></div>
                <div class="relative grid grid-cols-2 gap-6">
                    @php
                        $featureBox = 'p-6 rounded-2xl bg-white/80 dark:bg-white/5 ring-1 ring-slate-200/60 
                                     dark:ring-white/10 hover:scale-105 transition-transform duration-300';
                        $featureLabel = 'text-slate-600 dark:text-slate-300';
                    @endphp
                    <div class="{{ $featureBox }}">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-primary/10 text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="{{ $featureLabel }}">Layanan Instan</div>
                            </div>
                        </div>
                    </div>
                    <div class="{{ $featureBox }}">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-purple-500/10 text-purple-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">50+</div>
                                <div class="{{ $featureLabel }}">Layanan Media</div>
                            </div>
                        </div>
                    </div>
                    <div class="{{ $featureBox }}">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-green-500/10 text-green-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">100%</div>
                                <div class="{{ $featureLabel }}">Aman & Terpercaya</div>
                            </div>
                        </div>
                    </div>
                    <div class="{{ $featureBox }}">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-blue-500/10 text-blue-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-2xl font-bold">10K+</div>
                                <div class="{{ $featureLabel }}">Order Sukses</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Benefits --}}
    <section class="py-20 bg-gradient-to-b from-white/70 to-primary/5 dark:from-white/5 dark:to-primary/10">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="inline-block px-4 py-2 rounded-full bg-primary/10 text-primary font-medium text-sm mb-4">
                    Keunggulan Kami
                </span>
                <h2
                    class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Kenapa Memilih Sosialin?
                </h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">
                    Platform SMM Panel terbaik dengan berbagai keunggulan untuk membantu bisnis Anda
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="group p-8 rounded-2xl bg-white/80 dark:bg-white/5 shadow-lg ring-1 ring-slate-200/60 
                            dark:ring-white/10 hover:scale-105 transition-all duration-300">
                    <div
                        class="p-4 rounded-2xl bg-primary/10 text-primary inline-block mb-4 
                               group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Proses Instan</h3>
                    <p class="text-slate-600 dark:text-slate-300">
                        Layanan diproses secara otomatis 24/7 tanpa delay. Order Anda akan segera diproses setelah
                        pembayaran.
                    </p>
                </div>

                <div
                    class="group p-8 rounded-2xl bg-white/80 dark:bg-white/5 shadow-lg ring-1 ring-slate-200/60 
                            dark:ring-white/10 hover:scale-105 transition-all duration-300">
                    <div
                        class="p-4 rounded-2xl bg-primary/10 text-primary inline-block mb-4
                               group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Harga Bersaing</h3>
                    <p class="text-slate-600 dark:text-slate-300">
                        Dapatkan harga termurah untuk semua layanan. Tersedia berbagai paket sesuai dengan budget Anda.
                    </p>
                </div>

                <div
                    class="group p-8 rounded-2xl bg-white/80 dark:bg-white/5 shadow-lg ring-1 ring-slate-200/60 
                            dark:ring-white/10 hover:scale-105 transition-all duration-300">
                    <div
                        class="p-4 rounded-2xl bg-primary/10 text-primary inline-block mb-4
                               group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">100% Aman</h3>
                    <p class="text-slate-600 dark:text-slate-300">
                        Sistem kami menggunakan metode yang aman dan terpercaya. Data Anda terjamin keamanannya.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimoni Section --}}
    <section
        class="py-20 bg-gradient-to-b from-white/60 via-primary/5 to-purple-500/5 dark:from-white/5 dark:via-primary/10 dark:to-purple-500/10">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="inline-block px-4 py-2 rounded-full bg-primary/10 text-primary font-medium text-sm mb-4">
                    Testimonial
                </span>
                <h2
                    class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Apa Kata Mereka?
                </h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">
                    Ribuan pengguna telah mempercayai layanan kami untuk meningkatkan presence sosial media mereka
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="relative group">
                    <div
                        class="absolute inset-0.5 bg-gradient-to-r from-primary to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-500">
                    </div>
                    <div class="relative p-8 rounded-2xl bg-white dark:bg-slate-800 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                A
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Agung Prakoso</h3>
                                <p class="text-slate-600 dark:text-slate-400">Digital Marketer</p>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-primary/20 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                        <blockquote class="text-lg mb-6">
                            "Sangat puas dengan layanan Sosialin! Proses order super cepat dan engagement meningkat
                            drastis."
                        </blockquote>
                        <div class="flex gap-1">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <div
                        class="absolute inset-0.5 bg-gradient-to-r from-primary to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-500">
                    </div>
                    <div class="relative p-8 rounded-2xl bg-white dark:bg-slate-800 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                M
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Mira Setiani</h3>
                                <p class="text-slate-600 dark:text-slate-400">Content Creator</p>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-primary/20 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                        <blockquote class="text-lg mb-6">
                            "Platform terbaik untuk meningkatkan social media presence. Harga bersaing dan layanan premium!"
                        </blockquote>
                        <div class="flex gap-1">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <div
                        class="absolute inset-0.5 bg-gradient-to-r from-primary to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-500">
                    </div>
                    <div class="relative p-8 rounded-2xl bg-white dark:bg-slate-800 h-full">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                D
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg">Danang Wijaya</h3>
                                <p class="text-slate-600 dark:text-slate-400">Business Owner</p>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-primary/20 mb-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>
                        <blockquote class="text-lg mb-6">
                            "Interface modern dan support responsif. Sangat membantu bisnis saya berkembang di social
                            media!"
                        </blockquote>
                        <div class="flex gap-1">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('register') }}"
                    class="group inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-primary to-purple-600 
                          text-white font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <span>Mulai Sekarang</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
@endsection
