<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function __construct()
    {
        // Laravel 11 tidak support $this->middleware() -> pakai di routes saja
    }

    public function topupForm(Request $request)
    {
        return view('wallet.topup');
    }

    public function topupStore(Request $request, WalletService $walletService)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'], // minimal Rp 1.000
            'note'   => ['nullable', 'string', 'max:200'],
        ]);

        try {
            $user = $request->user();
            $walletService->ensure($user);
            $walletService->credit(
                $user,
                (float) $data['amount'],
                ['method' => 'manual', 'note' => $data['note'] ?? null]
            );

            return redirect()
                ->route('dashboard')
                ->with('status', 'Top-up berhasil. Saldo bertambah Rp ' . number_format($data['amount'], 2));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }
}
