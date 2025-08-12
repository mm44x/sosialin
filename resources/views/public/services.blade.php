@extends('layouts.marketing')

@section('content')
    <section class="container mx-auto px-4 py-10">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl md:text-3xl font-semibold">Daftar Layanan</h1>
            <form method="GET" action="{{ route('services.index') }}" class="flex items-center gap-2">
                <label for="search" class="sr-only">Cari layanan</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Cari layanan..."
                    class="px-3 py-2 rounded-xl border bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600" />
                <button class="px-3 py-2 rounded-xl bg-primary text-white">Cari</button>
            </form>
        </div>

        @if ($services->count() === 0)
            <div class="mt-8 p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <p class="text-slateText dark:text-slate-300">Belum ada layanan aktif. Coba lagi nanti.</p>
            </div>
        @else
            <div class="mt-6 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($services as $svc)
                    <article class="p-5 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                        <h3 class="font-semibold">{{ $svc->name }}</h3>
                        <p class="mt-1 text-sm text-slateText dark:text-slate-300">
                            {{ $svc->category->name ?? '-' }} · {{ $svc->provider->name ?? '-' }}
                        </p>
                        <dl class="mt-3 text-sm">
                            <div class="flex justify-between">
                                @php
                                    $fx = (float) env('FX_USD_IDR', 16000);
                                    $rateUSD = (float) $svc->rate;
                                    $rateIDR = $rateUSD * $fx;
                                @endphp
                                <div class="flex justify-between">
                                    <dt>Rate/1k (USD)</dt>
                                    <dd>$ {{ number_format($rateUSD, 4) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Rate/1k (IDR ≈)</dt>
                                    <dd>Rp {{ number_format($rateIDR, 2) }}</dd>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <dt>Min</dt>
                                <dd>{{ $svc->min }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>Max</dt>
                                <dd>{{ $svc->max }}</dd>
                            </div>
                        </dl>
                        <p class="mt-3 text-sm line-clamp-3">{{ $svc->description }}</p>
                        <div class="mt-4">
                            @auth
                                <a href="{{ route('orders.create', $svc) }}"
                                    class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary">
                                    Pesan
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary">
                                    Masuk untuk memesan
                                </a>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $services->links() }}
            </div>
        @endif
    </section>
@endsection
