@php
    $val = fn($k, $d = '') => old($k, $row->$k ?? $d);
@endphp

<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Jenis</label>
        <select name="type"
            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
            @foreach (['bank' => 'Bank Transfer', 'qris' => 'QRIS', 'ewallet' => 'E-Wallet', 'other' => 'Lainnya'] as $k => $label)
                <option value="{{ $k }}" @selected($val('type') === $k)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Nama Tampilan</label>
        <input name="name" value="{{ $val('name') }}" required
            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
            placeholder="mis. BCA • 1234567890 a.n PT Sosialin">
    </div>
</div>

<div class="grid sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium">Bank</label>
        <input name="bank_name" value="{{ $val('bank_name') }}"
            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
            placeholder="BCA/BNI/…">
    </div>
    <div>
        <label class="block text-sm font-medium">No. Rekening</label>
        <input name="account_number" value="{{ $val('account_number') }}"
            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
    </div>
    <div>
        <label class="block text-sm font-medium">a.n</label>
        <input name="account_name" value="{{ $val('account_name') }}"
            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Instruksi (opsional)</label>
    <textarea name="instructions" rows="4"
        class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600"
        placeholder="Contoh: Transfer sesuai nominal, lalu upload bukti.">{{ $val('instructions') }}</textarea>
</div>

<div class="grid sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium">Urutan</label>
        <input type="number" name="sort_order" value="{{ (int) $val('sort_order', 0) }}"
            class="mt-1 w-full px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 dark:text-white dark:border-slate-600">
    </div>
    <div class="flex items-center gap-2 mt-7">
        <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $row->is_active ?? true))>
        <label for="is_active" class="text-sm">Aktif</label>
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Gambar (QR/Logo) — opsional</label>
    <input type="file" name="media" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full text-sm">
    @if (!empty($row?->media_path))
        <div class="mt-2 text-sm">
            File saat ini:
            <a class="underline" href="{{ asset('storage/' . $row->media_path) }}" target="_blank">Lihat</a>
        </div>
    @endif
</div>

<div class="flex gap-2">
    <a href="{{ route('admin.payment-methods.index') }}"
        class="px-4 py-2 rounded-xl border dark:border-slate-600 hover:bg-primary/10">Kembali</a>
    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:opacity-90">Simpan</button>
</div>
