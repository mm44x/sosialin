<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function create(Request $request)
    {
        // Ambil metode pembayaran aktif (urutkan)
        $methods = PaymentMethod::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
            
        // Ambil riwayat top up user
        $topups = Topup::where('user_id', $request->user()->id)
            ->with('paymentMethod')
            ->latest()
            ->limit(5)
            ->get();

        return view('wallet.topup', [
            'methods' => $methods,
            'topups' => $topups,
            'balance' => optional($request->user()->wallet)->balance ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount'            => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'note'              => ['nullable', 'string', 'max:2000'],
            'proof'             => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $method = PaymentMethod::findOrFail((int) $data['payment_method_id']);

        // Simpan bukti
        $path = $request->file('proof')->store('topups', 'public');

        // Reference unik
        $ref = 'TP' . now()->format('YmdHis') . strtoupper(Str::random(4));

        Topup::create([
            'user_id'           => $request->user()->id,
            'payment_method_id' => $method->id,
            'method'            => $method->type, // simpan tipe metode (bank, ewallet, dll)
            'reference'         => $ref,
            'amount'            => (float) $data['amount'],
            'status'            => 'pending',
            'proof_path'        => $path,
            'note'              => $data['note'] ?? null,
            'meta'              => [
                'ip'          => $request->ip(),
                'ua'          => substr((string) $request->userAgent(), 0, 255),
                'method_name' => $method->name, // simpan nama lengkap metode di meta
            ],
        ]);

        return back()->with('status', "Request top up terkirim. Ref: {$ref}. Mohon tunggu verifikasi admin.");
    }

    public function transactions(Request $request)
    {
        $perPage = max(10, min(50, (int) $request->integer('per_page', 20)));

        $query = \App\Models\Transaction::where('user_id', $request->user()->id);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by transaction type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Handle sorting
        if ($request->filled('sort')) {
            $order = $request->input('order', 'desc');

            switch ($request->sort) {
                case 'date':
                    $query->orderBy('created_at', $order);
                    break;
                case 'amount':
                    $query->orderBy('amount', $order);
                    break;
                default:
                    $query->orderByDesc('id');
            }
        } else {
            $query->orderByDesc('id');
        }

        $rows = $query->paginate($perPage)->withQueryString();
        $balance = (float) optional($request->user()->wallet)->balance ?? 0.0;

        return view('wallet.transactions', [
            'rows'    => $rows,
            'balance' => $balance,
        ]);
    }
}
