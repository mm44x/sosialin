<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Tiket #{{ $ticket->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold">{{ $ticket->subject }}</div>
                        <div class="text-xs text-slate-500">
                            @if($ticket->order_id) Order: #{{ $ticket->order_id }} @else Tanpa Order @endif
                        </div>
                    </div>
                    <div>
                        @php $st = $ticket->status; @endphp
                        <span @class([
                            'inline-block px-3 py-1 rounded-xl text-xs font-medium ring-1 ring-inset',
                            'bg-green-100 text-green-800 ring-green-200' => $st==='open',
                            'bg-slate-100 text-slate-800 ring-slate-200' => $st!=='open',
                        ])>{{ ucfirst($st) }}</span>
                    </div>
                </div>

                {{-- Thread --}}
                <div class="mt-5 space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    @foreach($ticket->messages as $m)
                        <div @class([
                            'p-3 rounded-xl ring-1 text-sm',
                            $m->is_admin
                                ? 'bg-white/60 dark:bg-white/10 ring-slate-200/60 dark:ring-white/10'
                                : 'bg-primary/10 dark:bg-primary/20 ring-primary/30',
                            ])>
                            <div class="mb-1 text-xs text-slate-500">
                                {{ $m->is_admin ? 'Admin' : 'Anda' }} • {{ $m->created_at->format('d M Y H:i') }}
                            </div>
                            <div class="whitespace-pre-line">{{ $m->body }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('tickets.index') }}"
                       class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>

                    @if($ticket->isOpen())
                        <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                Tutup Tiket
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($ticket->isOpen())
                <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                    <form method="POST" action="{{ route('tickets.reply', $ticket) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium">Balasan</label>
                            <textarea name="message" rows="4" required
                                      class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                      placeholder="Tulis balasan Anda..."></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Kirim</button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
