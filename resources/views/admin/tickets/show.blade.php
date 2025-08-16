<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
                    Admin — Detail Tiket #{{ $ticket->id }}
                </h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Kelola dan balas tiket bantuan dari pengguna
                </p>
            </div>
            <nav class="flex space-x-4 text-sm">
                <a href="{{ route('admin.tickets.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary">
                    ← Kembali ke Daftar Tiket
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Ticket Info Card --}}
            <div class="p-6 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-xl bg-primary/10 text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $ticket->subject }}</h3>
                            <div class="flex items-center gap-3 mt-1 text-sm text-slate-500 dark:text-slate-400">
                                <span>User: {{ $ticket->user->name ?? '—' }}</span>
                                <span>•</span>
                                <span>{{ $ticket->user->email ?? '—' }}</span>
                                <span>•</span>
                                <span>Order ID: {{ $ticket->order_id ? '#' . $ticket->order_id : '—' }}</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Dibuat: {{ $ticket->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $badge = match ($ticket->status) {
                                'open'
                                    => 'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30',
                                'pending'
                                    => 'bg-yellow-100 text-yellow-800 ring-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:ring-yellow-400/30',
                                'closed'
                                    => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                                default
                                    => 'bg-slate-100 text-slate-800 ring-slate-200 dark:bg-slate-900/30 dark:text-slate-400 dark:ring-slate-400/30',
                            };
                        @endphp
                        <span class="inline-block px-3 py-2 rounded-xl text-sm font-medium ring-1 ring-inset {{ $badge }}">
                            {{ ucfirst($ticket->status) }}
                        </span>

                        {{-- Quick Status Switcher --}}
                        <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <select name="status"
                                    class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700
                                           bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                           focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                           focus:border-primary/20 dark:focus:border-primary/20
                                           transition-colors">
                                @foreach (['open','pending','closed'] as $opt)
                                    <option value="{{ $opt }}" @selected($ticket->status===$opt)>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-primary text-white font-medium
                                           hover:bg-primary/90 transition-colors">
                                Ubah
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Conversation Thread --}}
            <div class="rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-xl ring-1 ring-slate-200/60 dark:ring-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Percakapan Tiket</h3>
                        <span class="px-3 py-1 text-sm bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-lg">
                            {{ count($messages) }} pesan
                        </span>
                    </div>
                </div>

                <div class="max-h-[60vh] overflow-y-auto p-6 space-y-6">
                    @forelse ($messages as $msg)
                        @php
                            $isAdmin = $msg->is_admin;
                            $attachmentPath = $msg->meta['attachment_path'] ?? null;
                        @endphp
                        <div class="flex gap-4 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-tr from-primary to-purple-600 grid place-items-center text-sm font-bold text-white">
                                {{ $isAdmin ? 'A' : 'U' }}
                            </div>
                            <div class="max-w-[80%] {{ $isAdmin ? 'text-right' : '' }}">
                                <div class="{{ $isAdmin ? 'bg-gradient-to-r from-primary to-purple-600 text-white' : 'bg-slate-100 dark:bg-slate-700/50 dark:text-slate-100' }} px-4 py-3 rounded-2xl shadow-sm">
                                    @if(trim((string)$msg->body) !== '')
                                        <div class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $msg->body }}</div>
                                    @endif
                                    @if ($attachmentPath)
                                        <div class="mt-3 pt-3 border-t {{ $isAdmin ? 'border-white/20' : 'border-slate-200/60 dark:border-slate-600/60' }}">
                                            <a href="{{ route('admin.tickets.download', $msg) }}"
                                               class="inline-flex items-center gap-2 text-xs {{ $isAdmin ? 'text-white/90 hover:text-white' : 'text-slate-600 dark:text-slate-400 hover:text-primary' }} hover:underline transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Lampiran</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 {{ $isAdmin ? 'justify-end' : '' }}">
                                    <time>{{ optional($msg->created_at)->format('d M Y H:i') }}</time>
                                    <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                    <span class="font-medium">{{ $isAdmin ? 'Admin' : 'User' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">Belum ada percakapan</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Mulai percakapan dengan user di bawah ini</p>
                        </div>
                    @endforelse
                </div>

                {{-- Reply Form --}}
                <div class="border-t border-slate-200/60 dark:border-slate-700/60 p-6">
                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" enctype="multipart/form-data"
                          class="space-y-4">
                        @csrf
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="message">
                                Balasan Admin
                            </label>
                            <textarea name="message" id="message" rows="4" required
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                             bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                             placeholder-slate-400 dark:placeholder-slate-500
                                             focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                             focus:border-primary/20 dark:focus:border-primary/20
                                             transition-colors resize-none"
                                      placeholder="Tulis balasan untuk pengguna..."></textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="attachment">
                                    Lampiran (Opsional)
                                </label>
                                <input type="file" id="attachment" name="attachment"
                                       accept=".jpg,.jpeg,.png,.webp,.pdf"
                                       class="block w-full text-sm file:mr-4 file:px-4 file:py-2.5 file:rounded-xl
                                              file:border-0 file:bg-primary file:text-white file:font-medium
                                              file:hover:bg-primary/90 file:transition-colors
                                              border border-slate-200 dark:border-slate-700 rounded-xl
                                              bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                              focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                              focus:border-primary/20 dark:focus:border-primary/20
                                              transition-colors" />
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    JPG, PNG, WEBP, atau PDF maksimal 5MB
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="status">
                                    Ubah Status (Opsional)
                                </label>
                                <select name="status" id="status"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700
                                               bg-white dark:bg-slate-800 text-slate-900 dark:text-white
                                               focus:ring-2 focus:ring-primary/20 dark:focus:ring-primary/20 
                                               focus:border-primary/20 dark:focus:border-primary/20
                                               transition-colors">
                                    <option value="">— Biarkan status saat ini —</option>
                                    @foreach (['open','pending','closed'] as $opt)
                                        <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <a href="{{ route('admin.tickets.index') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                      border border-slate-200 dark:border-slate-700
                                      hover:border-primary/20 dark:hover:border-primary/20 
                                      hover:bg-primary/5 dark:hover:bg-primary/5
                                      text-slate-700 dark:text-slate-300
                                      transition-all duration-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <span>Kembali</span>
                            </a>
                            <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl 
                                           bg-gradient-to-r from-primary to-purple-600 
                                           text-white font-medium shadow-sm 
                                           hover:shadow-md transition-all duration-300 hover:scale-105
                                           focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span>Kirim Balasan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>