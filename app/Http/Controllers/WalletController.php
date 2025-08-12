<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Models\Transaction;

class WalletController extends Controller
{
    public function __construct()
    {
        // Laravel 11 tidak pakai $this->middleware() bawaan Controller
        // Lindungi via route group (lihat routes/web.php)
    }

    public function create()
    {
        return view('wallet.topup');
    }

    public function store(Request $request, WalletService $wallet)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'], // atur minimal sesuai kebijakan
            'note'   => ['nullable', 'string', 'max:190'],
        ]);

        $amount = round((float) $data['amount'], 2);
        $wallet->credit($request->user()->id, $amount, 'topup', [
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('status', 'Top-up berhasil: Rp ' . number_format($amount, 2));
    }

    public function transactions(Request $request)
    {
        $tx = Transaction::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('wallet.transactions', ['tx' => $tx]);
    }
}
