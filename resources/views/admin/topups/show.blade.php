<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Top Up #{{ $topup->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500">Reference</div>
                        <div class="font-medium">{{ $topup->reference }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">User</div>
                        <div class="font-medium">{{ $topup->user->name ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ $topup->user->email ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Amount</div>
                        <div class="font-medium">Rp {{ number_format((float) $topup->amount, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Method</div>
                        <div class="font-medium">{{ strtoupper($topup->method ?? '-') }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Status</div>
                        <div class="font-medium">{{ ucfirst($topup->status) }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Dibuat</div>
                        <div class="font-medium">{{ $topup->created_at?->format('d M Y H:i') }}</div>
                    </div>
                    @if ($topup->reviewed_at)
                        <div>
                            <div class="text-slate-500">Direview</div>
                            <div class="font-medium">{{ $topup->reviewed_at->format('d M Y H:i') }}</div>
                        </div>
                    @endif
                    @if ($topup->note)
                        <div class="sm:col-span-2">
                            <div class="text-slate-500">Catatan</div>
                            <div class="font-medium whitespace-pre-wrap">{{ $topup->note }}</div>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <div class="text-slate-500">Bukti</div>
                    @php
                        $isPdf = str_ends_with(strtolower($topup->proof_path ?? ''), '.pdf');
                    @endphp

                    @if ($topup->proof_path)
                        @if ($isPdf)
                            <a href="{{ asset('storage/' . $topup->proof_path) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                Lihat PDF
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $topup->proof_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $topup->proof_path) }}" alt="Bukti pembayaran"
                                    class="mt-2 max-h-96 rounded-xl ring-1 ring-slate-200/60 dark:ring-white/10">
                            </a>
                        @endif
                    @else
                        <div class="text-sm text-slate-500">Tidak ada bukti.</div>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ url()->previous() }}"
                        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>

                    @if ($topup->isPending())
                        <form method="POST" action="{{ route('admin.topups.approve', $topup) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">
                                Approve & Kredit Saldo
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.topups.reject', $topup) }}"
                            class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="note" placeholder="Alasan (opsional)"
                                class="px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                            <button class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
