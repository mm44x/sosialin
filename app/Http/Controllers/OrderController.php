<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Service $service)
    {
        $baseRateUSD = (float) $service->rate; // rate/1000 dari JAP (USD)
        $markup      = (float) ($service->provider->markup_percent ?? 0);
        $rateUSDwithMarkup = $baseRateUSD * (1 + ($markup / 100));

        // konversi ke IDR
        $fx = (float) env('FX_USD_IDR', 16000);
        $rateIDRwithMarkup = $rateUSDwithMarkup * $fx;

        return view('orders.create', [
            'service'         => $service,
            'baseRateUSD'     => $baseRateUSD,
            'markup'          => $markup,
            'rateUSDwithMarkup' => $rateUSDwithMarkup,
            'rateIDRwithMarkup' => $rateIDRwithMarkup,
            'fx'              => $fx,
        ]);
    }

    public function store(Request $request, \App\Models\Service $service)
    {
        $request->validate([
            'link'     => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'integer', 'min:' . $service->min, 'max:' . $service->max],
        ]);

        // Hitung biaya (rate/1000 + markup)
        // rate/1000 USD dari provider
        $baseRateUSD = (float) $service->rate;
        $markup      = (float) ($service->provider->markup_percent ?? 0);
        $rateUSDwithMarkup = $baseRateUSD * (1 + ($markup / 100));

        $qty = (int) $request->integer('quantity');

        // konversi ke IDR
        $fx = (float) env('FX_USD_IDR', 16000);
        $rateIDRwithMarkup = $rateUSDwithMarkup * $fx;

        // biaya IDR sebelum pembulatan 2 desimal
        $rawCostIDR = $rateIDRwithMarkup * ($qty / 1000);

        // hindari jadi 0.00 setelah pembulatan (gunakan minimal charge)
        $minCharge  = (float) env('MIN_ORDER_CHARGE_IDR', 0.01);
        $cost = max($minCharge, round($rawCostIDR, 2));


        // 1) Buat order lokal (pending) lebih dulu untuk bisa referensi ke transaksi debit
        $order = \App\Models\Order::create([
            'user_id'           => $request->user()->id,
            'service_id'        => $service->id,
            'link'              => (string) $request->input('link'),
            'quantity'          => $qty,
            'status'            => \App\Models\Order::STATUS_PENDING,
            'provider_order_id' => null,
            'cost'              => $cost, // dalam IDR
            'meta'              => [
                // simpan info lengkap supaya transparan
                'base_rate_usd'      => $baseRateUSD,       // rate/1000 sebelum markup (USD)
                'markup'             => $markup,            // persen
                'rate_1000_usd'      => $rateUSDwithMarkup, // sesudah markup (USD)
                'fx_usd_idr'         => $fx,                // kurs yang dipakai
                'rate_1000_idr'      => $rateIDRwithMarkup, // sesudah markup (IDR)
                'raw_cost_idr'       => $rawCostIDR,        // sebelum pembulatan
                'min_order_charge'   => $minCharge,         // batas minimal biaya
                'currency'           => 'IDR',
            ],
        ]);

        // 2) Potong saldo (debit). Jika saldo kurang, tandai order error & hentikan.
        try {
            $walletService = new \App\Services\WalletService();
            $walletService->ensure($request->user());
            $walletService->debit(
                $request->user(),
                $cost,
                ['reason' => 'order', 'order_id' => $order->id],
                $order->id
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            $order->update([
                'status' => \App\Models\Order::STATUS_ERROR,
                'meta'   => array_merge($order->meta ?? [], ['insufficient_balance' => true, 'validation' => $e->errors()]),
            ]);
            return back()
                ->withErrors(['balance' => 'Saldo tidak cukup untuk membuat pesanan ini.'])
                ->withInput();
        } catch (\Throwable $e) {
            // Error tak terduga saat debit
            $order->update([
                'status' => \App\Models\Order::STATUS_ERROR,
                'meta'   => array_merge($order->meta ?? [], ['debit_exception' => $e->getMessage()]),
            ]);
            return back()->withErrors(['amount' => 'Gagal memproses saldo: ' . $e->getMessage()])->withInput();
        }

        // 3) Kirim ke JAP (add). Jika gagal, lakukan refund (credit) lalu tandai order error.
        try {
            $client = new \App\Services\Smm\JapClient(
                baseUrl: env('JAP_BASE_URL'),
                apiKey: env('JAP_API_KEY'),
                providerId: $service->provider_id
            );

            $payload = [
                'service'  => (string) $service->external_service_id,
                'link'     => (string) $request->input('link'),
                'quantity' => $qty,
            ];
            $resp = $client->addOrder($payload);

            $provId = $resp['order'] ?? $resp['order_id'] ?? $resp['id'] ?? null;
            if (!$provId) {
                // Refund karena provider tidak mengembalikan ID
                (new \App\Services\WalletService())->credit(
                    $request->user(),
                    $cost,
                    ['reason' => 'refund_no_provider_id', 'order_id' => $order->id],
                    $order->id,
                    'refund'
                );

                $order->update([
                    'status' => \App\Models\Order::STATUS_ERROR,
                    'meta'   => array_merge($order->meta ?? [], ['provider_response' => $resp]),
                ]);

                return redirect()->route('dashboard')
                    ->with('status', "Order #{$order->id} gagal di provider (tanpa ID). Saldo sudah dikembalikan.");
            }

            // Sukses — simpan provider ID & set processing
            $order->update([
                'provider_order_id' => (string) $provId,
                'status'            => \App\Models\Order::STATUS_PROCESSING,
                'meta'              => array_merge($order->meta ?? [], ['provider_response' => $resp]),
            ]);

            return redirect()->route('dashboard')
                ->with('status', "Order #{$order->id} dikirim (ID provider: {$provId}). Biaya: Rp " . number_format($cost, 2));
        } catch (\Throwable $e) {
            // Refund karena exception saat kirim order ke provider
            try {
                (new \App\Services\WalletService())->credit(
                    $request->user(),
                    $cost,
                    ['reason' => 'refund_exception', 'order_id' => $order->id, 'exception' => $e->getMessage()],
                    $order->id,
                    'refund'
                );
            } catch (\Throwable $refundEx) {
                // Catat jika refund juga bermasalah (saldo tetap aman di log)
                $order->update([
                    'meta' => array_merge($order->meta ?? [], ['refund_exception' => $refundEx->getMessage()]),
                ]);
            }

            $order->update([
                'status' => \App\Models\Order::STATUS_ERROR,
                'meta'   => array_merge($order->meta ?? [], ['exception' => $e->getMessage()]),
            ]);

            return redirect()->route('dashboard')
                ->with('status', "Order #{$order->id} gagal dikirim: " . $e->getMessage() . " — saldo telah dikembalikan.");
        }
    }
}
