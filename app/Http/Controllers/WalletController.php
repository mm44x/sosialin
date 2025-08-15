<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function create(Request $request)
    {
        // Bisa ambil instruksi dari config/env — di sini contoh hardcoded sederhana
        $paymentInfo = [
            'bank_accounts' => [
                ['bank' => 'BCA', 'name' => 'PT Sosialin', 'number' => '1234567890'],
                ['bank' => 'BNI', 'name' => 'PT Sosialin', 'number' => '9876543210'],
            ],
            'qris_image' => 'images/qris-example.png', // letakkan file di public/images/ atau storage symlink
            'note_html'  => '<b>Catatan:</b> Sertakan nominal tepat & unggah bukti yang jelas.',
        ];

        return view('wallet.topup', compact('paymentInfo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'method' => ['nullable', 'string', 'max:30'], // bank|qris|other
            'note'   => ['nullable', 'string', 'max:2000'],
            'proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'], // <=5MB
        ]);

        // Simpan bukti
        $path = $request->file('proof')->store('topups', 'public');

        // Reference unik
        $ref = 'TP' . now()->format('YmdHis') . strtoupper(Str::random(4));

        Topup::create([
            'user_id'    => $request->user()->id,
            'reference'  => $ref,
            'method'     => $data['method'] ?? null,
            'amount'     => (float)$data['amount'],
            'status'     => 'pending',
            'proof_path' => $path,
            'note'       => $data['note'] ?? null,
            'meta'       => [
                'ip'      => $request->ip(),
                'ua'      => substr((string)$request->userAgent(), 0, 255),
                'version' => 1,
            ],
        ]);

        return back()->with('status', "Request top up terkirim. Ref: {$ref}. Mohon tunggu verifikasi admin.");
    }

    public function transactions(Request $request)
    {
        $perPage = max(10, min(50, (int) $request->integer('per_page', 20)));

        $rows = \App\Models\Transaction::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $balance = (float) optional($request->user()->wallet)->balance ?? 0.0;

        return view('wallet.transactions', [
            'rows'    => $rows,
            'balance' => $balance,
        ]);
    }
}
