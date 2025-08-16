<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Ticket #{{ $ticket->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header / Status --}}
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold">{{ $ticket->subject }}</div>
                        <div class="text-sm text-slate-500">
                            User: {{ $ticket->user->name ?? '—' }} ({{ $ticket->user->email ?? '—' }})
                        </div>
                        <div class="text-sm text-slate-500">Order ID: {{ $ticket->order_id ?: '—' }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        @php $badge = match($ticket->status){
                            'open'    => 'bg-blue-100 text-blue-800 ring-blue-200',
                            'pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                            'closed'  => 'bg-slate-200 text-slate-800 ring-slate-300',
                            default   => 'bg-slate-100 text-slate-800 ring-slate-200',
                        }; @endphp
                        <span class="inline-block px-3 py-1 rounded-xl text-xs font-medium ring-1 ring-inset {{ $badge }}">
                            {{ ucfirst($ticket->status) }}
                        </span>

                        {{-- Quick status switcher --}}
                        <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
                            @csrf @method('PUT')
                            <select name="status"
                                    class="h-9 px-3 py-1 rounded-xl border
                                           bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                                @foreach (['open','pending','closed'] as $opt)
                                    <option value="{{ $opt }}" @selected($ticket->status===$opt)>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                            <button class="h-9 px-3 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Ubah</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Thread (scrollable) --}}
            <div class="p-0 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10 overflow-hidden">
                <div class="max-h-[60vh] overflow-y-auto p-6 space-y-4">
                    @forelse ($messages as $msg)
                        @php
                            $isAdmin = $msg->is_admin;
                            $attachmentPath = $msg->meta['attachment_path'] ?? null;
                        @endphp
                        <div class="flex gap-3 {{ $isAdmin ? 'flex-row-reverse text-right' : '' }}">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 grid place-items-center text-xs font-semibold">
                                {{ $isAdmin ? 'A' : 'U' }}
                            </div>
                            <div class="max-w-[85%]">
                                <div class="{{ $isAdmin ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 dark:text-slate-100' }} px-4 py-3 rounded-2xl">
                                    @if(trim((string)$msg->body) !== '')
                                        <div class="whitespace-pre-wrap break-words text-sm">{{ $msg->body }}</div>
                                    @endif
                                    @if ($attachmentPath)
                                        <div class="mt-2 text-xs">
                                            <a href="{{ route('admin.tickets.download', $msg) }}"
                                               class="underline hover:no-underline">Lampiran</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ optional($msg->created_at)->format('d M Y H:i') }}
                                    {{ $isAdmin ? '• Admin' : '• User' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada percakapan.</div>
                    @endforelse
                </div>

                {{-- Reply box --}}
                <div class="border-t border-slate-200/60 dark:border-white/10 p-4">
                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" enctype="multipart/form-data"
                          class="grid md:grid-cols-12 gap-3">
                        @csrf
                        <div class="md:col-span-9">
                            <label class="block text-sm font-medium">Balasan</label>
                            <textarea name="message" rows="3"
                                      class="mt-1 w-full px-3 py-2 rounded-xl border
                                             bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
                                      placeholder="Tulis balasan untuk pengguna..."></textarea>
                        </div>
                        <div class="md:col-span-3 space-y-3">
                            <div>
                                <label class="block text-sm font-medium">Lampiran (opsional)</label>
                                <input type="file" name="attachment"
                                       class="mt-1 block w-full text-sm file:mr-3 file:px-3 file:py-2 file:rounded-xl
                                              file:border file:bg-white dark:file:bg-slate-800 dark:file:text-white
                                              file:border-slate-300 dark:file:border-slate-600
                                              border rounded-xl dark:bg-slate-800 dark:text-white dark:border-slate-600" />
                                <p class="text-xs text-slate-500 mt-1">jpg, png, webp, pdf (≤ 5MB)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Set status</label>
                                <select name="status"
                                        class="mt-1 w-full h-10 px-3 py-2 rounded-xl border
                                               bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
                                    <option value="">— Biarkan —</option>
                                    @foreach (['open','pending','closed'] as $opt)
                                        <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.tickets.index') }}"
                                   class="h-10 px-4 rounded-xl border dark:border-slate-600 hover:bg-primary/10 flex items-center justify-center">
                                    Kembali
                                </a>
                                <button class="h-10 px-4 rounded-xl bg-primary text-white hover:opacity-90">
                                    Kirim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>