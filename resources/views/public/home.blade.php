@extends('layouts.marketing')

@section('content')
    {{-- Hero --}}
    <section class="bg-surface-light dark:bg-surface-dark text-slate-900 dark:text-slate-100">
        <div class="container mx-auto px-4 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h1 class="text-3xl md:text-5xl font-bold leading-tight">
                    SMM Panel Cepat & Andal untuk Skala Anda
                </h1>
                <p class="mt-4 text-slate-600 dark:text-slate-300">
                    Kelola dan otomatisasi pesanan layanan sosial media dari berbagai provider.
                    Sinkron layanan, markup harga, dan tracking status — semuanya di satu tempat.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}"
                        class="px-5 py-3 rounded-2xl bg-primary text-white shadow-soft
                              hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        Daftar Gratis
                    </a>
                    <a href="{{ route('services.index') }}"
                        class="px-5 py-3 rounded-2xl border border-slate-300 dark:border-slate-600
                              text-slate-900 dark:text-slate-100 bg-white/70 dark:bg-white/5
                              hover:bg-primary/10 dark:hover:bg-white/10
                              focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                        Lihat Layanan
                    </a>
                </div>
            </div>

            <div class="bg-white/60 dark:bg-white/5 rounded-2xl p-6 shadow-soft ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @php
                        $featureBox =
                            'p-4 rounded-xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10';
                        $featureLabel = 'text-slate-600 dark:text-slate-300';
                    @endphp
                    <div class="{{ $featureBox }}">
                        <div class="text-3xl font-bold">24/7</div>
                        <div class="{{ $featureLabel }}">Otomasi</div>
                    </div>
                    <div class="{{ $featureBox }}">
                        <div class="text-3xl font-bold">+JAP</div>
                        <div class="{{ $featureLabel }}">Provider</div>
                    </div>
                    <div class="{{ $featureBox }}">
                        <div class="text-3xl font-bold">Secure</div>
                        <div class="{{ $featureLabel }}">CSRF & Rate-limit</div>
                    </div>
                    <div class="{{ $featureBox }}">
                        <div class="text-3xl font-bold">Logs</div>
                        <div class="{{ $featureLabel }}">API & Retry</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Benefits --}}
    <section class="py-14 bg-white/70 dark:bg-white/5">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-slate-100">
                Kenapa pilih Sosialin?
            </h2>
            <ul class="mt-6 grid md:grid-cols-3 gap-6">
                <li
                    class="p-5 rounded-2xl bg-white dark:bg-white/5 shadow-soft ring-1 ring-slate-200/60 dark:ring-white/10">
                    <h3 class="font-semibold">Sinkron Layanan</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        Tarik daftar dari provider & simpan lokal.
                    </p>
                </li>
                <li
                    class="p-5 rounded-2xl bg-white dark:bg-white/5 shadow-soft ring-1 ring-slate-200/60 dark:ring-white/10">
                    <h3 class="font-semibold">Markup Fleksibel</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        Atur margin per layanan/provider.
                    </p>
                </li>
                <li
                    class="p-5 rounded-2xl bg-white dark:bg-white/5 shadow-soft ring-1 ring-slate-200/60 dark:ring-white/10">
                    <h3 class="font-semibold">Tracking Status</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        Pantau progress order dengan polling terjadwal.
                    </p>
                </li>
            </ul>
        </div>
    </section>

    {{-- Testimoni (placeholder) --}}
    <section class="py-14 bg-white/60 dark:bg-white/5">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-slate-100">
                Apa kata pengguna?
            </h2>
            <div class="mt-6 grid md:grid-cols-3 gap-6 text-sm">
                @php $quoteBox = 'p-5 rounded-2xl bg-white dark:bg-white/5 shadow-soft ring-1 ring-slate-200/60 dark:ring-white/10'; @endphp
                <blockquote class="{{ $quoteBox }}">
                    “Proses order jadi rapi & cepat.”
                    <span class="block mt-2 text-slate-600 dark:text-slate-300">— Agung</span>
                </blockquote>
                <blockquote class="{{ $quoteBox }}">
                    “Sinkron JAP mulus, hemat waktu.”
                    <span class="block mt-2 text-slate-600 dark:text-slate-300">— Mira</span>
                </blockquote>
                <blockquote class="{{ $quoteBox }}">
                    “UI clean, mudah dipakai.”
                    <span class="block mt-2 text-slate-600 dark:text-slate-300">— Danang</span>
                </blockquote>
            </div>
        </div>
    </section>
@endsection
