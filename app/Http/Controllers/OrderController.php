<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\OrderSubmitJob;

class OrderController extends Controller
{
    public function create(\App\Models\Service $service)
    {
        $ps = app(\App\Services\PricingService::class);
        $bd = $ps->breakdown($service, $service->min);

        return view('orders.create', [
            'service'           => $service,
            'baseRateUSD'       => $bd['baseRateUSD'],
            'markup'            => $bd['usedMarkup'],
            'rateUSDwithMarkup' => $bd['rateUSDwithMarkup'],
            'ratePerThousand'   => $bd['ratePerThousandLocal'],
            'billingMin'        => $bd['min'],
            'mult'              => $bd['mult'],
        ]);
    }



    public function store(Request $request, Service $service)
    {
        $request->validate([
            'link'     => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'integer', 'min:' . $service->min, 'max:' . $service->max],
        ]);

        $qty = (int) $request->integer('quantity');

        // === Satu-satunya sumber kebenaran harga ===
        $ps = app(\App\Services\PricingService::class);
        $bd = $ps->breakdown($service, $qty);

        $cost           = $bd['cost'];                 // FINAL — gunakan ini saja
        $baseRate       = $bd['baseRateUSD'];
        $markup         = $bd['usedMarkup'];
        $rateWithMarkup = $bd['rateUSDwithMarkup'];    // USD / 1000 (after markup)

        $userId = (int) $request->user()->id;

        // 1) Potong saldo + buat order pending secara atomik (tanpa call HTTP di dalam transaksi)
        try {
            app(\App\Services\WalletService::class)->debit($userId, $cost, [
                'reason'      => 'reserve_for_order',
                'service_id'  => $service->id,
                'qty'         => $qty,
                'rate_1000_usd'   => $rateWithMarkup,
                'rate_1000_local' => $bd['ratePerThousandLocal'],
            ]);
        } catch (\Throwable $e) {
            // Saldo tidak cukup atau error wallet lainnya
            return back()->withErrors(['quantity' => 'Gagal memproses: ' . $e->getMessage()])->withInput();
        }

        // Buat order lokal (pending)
        $order = \App\Models\Order::create([
            'user_id'    => $userId,
            'service_id' => $service->id,
            'link'       => $request->string('link'),
            'quantity'   => $qty,
            'status'     => \App\Models\Order::STATUS_PENDING,
            'cost'       => $cost, // ← tetap dari $bd
            'meta'       => [
                'base_rate_usd'        => $baseRate,
                'markup_used_percent'  => $markup,
                'rate_1000_usd'        => $rateWithMarkup,
                'rate_1000_local'      => $bd['ratePerThousandLocal'],
                'multiplier'           => $bd['mult'],
                'min_charge'           => $bd['min'],
            ],
        ]);

        // ...setelah $order dibuat:
        $idempotencyKey = (string) Str::uuid();
        $meta = $order->meta ?? [];
        $meta['idempotency_key'] = $idempotencyKey;
        $order->update(['meta' => $meta]);

        OrderSubmitJob::dispatch($order->id, $idempotencyKey);

        // Redirect cepat ke dashboard
        return redirect()
            ->route('dashboard')
            ->with('status', "Order #{$order->id} dibuat & sedang diproses. Saldo terpotong Rp " . number_format($cost, 2));
    }
}
