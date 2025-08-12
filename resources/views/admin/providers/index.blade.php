<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Admin — Providers</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 text-green-800 ring-1 ring-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div
                class="overflow-x-auto rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <table class="min-w-full text-sm">
                    <thead class="text-left">
                        <tr>
                            <th class="py-2 px-4">Nama</th>
                            <th class="py-2 px-4">Base URL</th>
                            <th class="py-2 px-4">Markup %</th>
                            <th class="py-2 px-4">Aktif</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($providers as $p)
                            <tr class="border-t border-slate-200/60 dark:border-white/10">
                                <td class="py-2 px-4 font-medium">{{ $p->name }}</td>
                                <td class="py-2 px-4 text-slateText dark:text-slate-300 truncate">{{ $p->base_url }}
                                </td>
                                <td class="py-2 px-4">{{ number_format($p->markup_percent, 2) }}</td>
                                <td class="py-2 px-4">
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs
                  @class([
                      'bg-green-100 text-green-800' => $p->active,
                      'bg-red-100 text-red-800' => !$p->active,
                  ])">
                                        {{ $p->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('admin.providers.edit', $p) }}"
                                        class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-primary/10">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 px-4" colspan="5">Belum ada provider.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
