<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl dark:text-white">Admin — Edit Provider #{{ $provider->id }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 rounded-2xl bg-white dark:bg-white/5 ring-1 ring-slate-200/60 dark:ring-white/10">
                <form method="POST" action="{{ route('admin.providers.update', $provider) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input name="name" value="{{ old('name', $provider->name) }}"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Tipe (opsional)</label>
                            <input name="type" value="{{ old('type', $provider->type) }}"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            @error('type')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Markup Default (%)</label>
                            <input type="number" step="0.01" min="0" max="1000" name="markup_percent"
                                value="{{ old('markup_percent', $provider->markup_percent) }}"
                                class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            @error('markup_percent')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Base URL</label>
                        <input name="base_url" value="{{ old('base_url', $provider->base_url) }}"
                            placeholder="https://justanotherpanel.com/api/v2"
                            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                        @error('base_url')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">API Key (opsional, isi untuk mengganti)</label>
                        <div class="flex gap-2">
                            <input id="api_key" name="api_key" type="password" autocomplete="new-password"
                                placeholder="•••••••• (kosongkan jika tidak ingin mengganti)"
                                class="flex-1 mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <button type="button" id="toggleApiKey"
                                class="mt-1 px-3 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">
                                Lihat
                            </button>
                        </div>
                        @error('api_key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-slateText dark:text-slate-300 mt-1">Catatan: demi keamanan, API key tidak
                            ditampilkan. Isi untuk mengganti.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="active" value="0">
                        <input id="active" name="active" type="checkbox" value="1"
                            @checked(old('active', $provider->active))>
                        <label for="active" class="text-sm">Active</label>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.providers.index') }}"
                            class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
                        <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const inp = document.getElementById('api_key');
                const btn = document.getElementById('toggleApiKey');

                async function fetchApiKeyOnce() {
                    // Jika input masih kosong, ambil dari server
                    if (!inp.value) {
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content');
                            const url = "{{ route('admin.providers.reveal-key', $provider) }}";

                            const res = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                            });

                            if (!res.ok) {
                                throw new Error('Gagal mengambil API key (' + res.status + ')');
                            }

                            const data = await res.json();
                            inp.value = data.api_key || '';
                        } catch (e) {
                            alert(e.message || 'Gagal mengambil API key.');
                        }
                    }
                }

                if (inp && btn) {
                    btn.addEventListener('click', async () => {
                        await fetchApiKeyOnce(); // ambil dulu jika kosong
                        // Toggle tampilan
                        inp.type = (inp.type === 'password') ? 'text' : 'password';
                        btn.textContent = (inp.type === 'password') ? 'Lihat' : 'Sembunyikan';
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
