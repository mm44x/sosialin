{{-- Path : resources/views/orders/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Riwayat Order
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Pantau dan kelola semua order Anda di satu tempat
                </p>
            </div>

            <a href="{{ route('services.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                      border border-slate-200 dark:border-slate-700
                      hover:border-primary/20 dark:hover:border-primary/20 
                      hover:bg-primary/5 dark:hover:bg-primary/5
                      text-slate-700 dark:text-slate-300
                      transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Order Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Filter Card --}}
            <div
                class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl 
                        ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <form method="GET" action="{{ route('orders.index') }}" class="space-y-6">
                    <div class="grid md:grid-cols-8 gap-6">
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="search">
                                Cari Order
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="search" name="search"
                                    value="{{ $filters['search'] ?? request('search') }}"
                                    placeholder="Cari berdasarkan ID, link, atau nama layanan..."
                                    class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700
                                              bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                              focus:border-primary/20 dark:focus:border-primary/20
                                              transition-colors">
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                Contoh: #123, instagram.com/..., atau nama layanan
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="status">
                                Status
                            </label>
                            @php $st = strtolower($filters['status'] ?? request('status')); @endphp
                            <select id="status" name="status"
                                class="mt-1 block w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 
                                       bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                       focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20
                                       focus:border-primary/20 dark:focus:border-primary/20
                                       transition-colors">
                                <option value="">Semua Status</option>
                                @foreach (['pending', 'processing', 'completed', 'partial', 'canceled', 'error'] as $opt)
                                    <option value="{{ $opt }}" @selected($st === $opt)>
                                        {{ ucfirst($opt) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 invisible">
                                Aksi
                            </label>
                            <div class="mt-1 flex items-center gap-2">
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
                                           bg-gradient-to-r from-primary to-purple-600 
                                           text-white font-medium shadow-sm 
                                           hover:shadow-md transition-all duration-300 hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    <span>Filter</span>
                                </button>
                                <a href="{{ route('orders.index') }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl 
                                          border border-slate-200 dark:border-slate-700
                                          hover:border-primary/20 dark:hover:border-primary/20 
                                          hover:bg-primary/5 dark:hover:bg-primary/5
                                          font-medium transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Reset</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            {{-- Orders Table --}}
            <div
                class="overflow-hidden rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl 
                        ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200/60 dark:divide-slate-700/60">
                        <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Order ID
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Layanan
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Jumlah
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Biaya
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Status
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-left text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Terakhir Update
                                </th>
                                <th scope="col"
                                    class="py-3.5 px-4 text-right text-xs font-medium uppercase tracking-wide
                                                     text-slate-500 dark:text-slate-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-slate-200/60 dark:divide-slate-700/60 bg-white/50 dark:bg-slate-800/30">
                            @forelse($orders as $o)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="whitespace-nowrap py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-medium text-slate-900 dark:text-white">#{{ $o->id }}</span>
                                            <button type="button"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-primary 
                                                       hover:bg-primary/10 focus:outline-none focus:ring-2 
                                                       focus:ring-primary/20 transition-colors js-copy-id"
                                                data-copy="{{ $o->id }}"
                                                aria-label="Salin Order ID {{ $o->id }}" title="Salin Order ID">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap py-4 px-4">
                                        <div class="text-sm text-slate-900 dark:text-white">
                                            {{ $o->service->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap py-4 px-4">
                                        <div class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ number_format($o->quantity, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap py-4 px-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                            Rp {{ number_format($o->cost, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    @php $st = strtolower($o->status ?? ''); @endphp
                                    <td class="whitespace-nowrap py-4 px-4">
                                        <span @class([
                                            'inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium',
                                            // Status variations
                                            'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-600/20' => in_array($st, [
                                                'pending',
                                                'processing',
                                            ]),
                                            'bg-green-50 text-green-700 ring-1 ring-green-600/20' =>
                                                $st === 'completed',
                                            'bg-orange-50 text-orange-700 ring-1 ring-orange-600/20' =>
                                                $st === 'partial',
                                            'bg-red-50 text-red-700 ring-1 ring-red-600/20' => in_array($st, [
                                                'canceled',
                                                'cancelled',
                                                'error',
                                            ]),
                                            'bg-slate-50 text-slate-700 ring-1 ring-slate-600/20' => !in_array($st, [
                                                'pending',
                                                'processing',
                                                'completed',
                                                'partial',
                                                'canceled',
                                                'cancelled',
                                                'error',
                                            ]),
                                        ])>
                                            @if (in_array($st, ['pending', 'processing']))
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @elseif($st === 'completed')
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @elseif($st === 'partial')
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M7 16V4M7 4L3 8m4-4l4 4" />
                                                </svg>
                                            @elseif(in_array($st, ['canceled', 'cancelled', 'error']))
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @endif
                                            {{ ucfirst($st) ?: 'Unknown' }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap py-4 px-4">
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $o->updated_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap py-4 px-4 text-right">
                                        <a href="{{ route('orders.show', $o) }}"
                                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg
                                                  border border-slate-200 dark:border-slate-700
                                                  text-sm font-medium text-slate-700 dark:text-slate-300
                                                  hover:border-primary/20 dark:hover:border-primary/20 
                                                  hover:bg-primary/5 dark:hover:bg-primary/5
                                                  transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 px-4 text-center">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div
                                                class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 
                                                        flex items-center justify-center text-slate-400 dark:text-slate-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-medium text-slate-900 dark:text-white">
                                                Belum Ada Order
                                            </h3>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                Mulai order layanan untuk melihat riwayat di sini
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @push('scripts')
                <script>
                    // Modern copy Order ID functionality with visual feedback
                    document.addEventListener('DOMContentLoaded', () => {
                        document.querySelectorAll('.js-copy-id').forEach(btn => {
                            btn.addEventListener('click', async () => {
                                const id = btn.getAttribute('data-copy');
                                let ok = false;

                                // Try modern API first
                                if (navigator.clipboard && window.isSecureContext) {
                                    try {
                                        await navigator.clipboard.writeText(id);
                                        ok = true;
                                    } catch (e) {
                                        ok = false;
                                    }
                                }
                                // Fallback for older browsers
                                if (!ok) {
                                    const ta = document.createElement('textarea');
                                    ta.value = id;
                                    ta.style.position = 'fixed';
                                    ta.style.opacity = '0';
                                    document.body.appendChild(ta);
                                    ta.select();
                                    try {
                                        ok = document.execCommand('copy');
                                    } catch (e) {
                                        ok = false;
                                    }
                                    document.body.removeChild(ta);
                                }

                                // Enhanced visual feedback
                                const originalContent = btn.innerHTML;
                                const originalClasses = btn.className;

                                btn.disabled = true;

                                if (ok) {
                                    // Success state
                                    btn.className =
                                        'p-1.5 rounded-lg text-green-500 bg-green-50 dark:bg-green-500/10 ring-1 ring-green-500/20';
                                    btn.innerHTML = `<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M5 13l4 4L19 7"/>
                                        </svg>`;
                                } else {
                                    // Error state
                                    btn.className =
                                        'p-1.5 rounded-lg text-red-500 bg-red-50 dark:bg-red-500/10 ring-1 ring-red-500/20';
                                    btn.innerHTML = `<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>`;
                                }

                                // Reset after animation
                                setTimeout(() => {
                                    btn.className = originalClasses;
                                    btn.innerHTML = originalContent;
                                    btn.disabled = false;
                                }, 1500);
                            });
                        });
                    });
                </script>
            @endpush

        </div>

        {{-- Pagination --}}
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-6 px-4 sm:px-0">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
