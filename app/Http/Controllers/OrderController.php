<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Service $service)
    {
        // 1) Rate dasar USD per 1000 dari provider + markup
        $baseRateUSD       = (float) $service->rate; // USD / 1000
        $markup            = (float) ($service->provider->markup_percent ?? 0);
        $rateUSDwithMarkup = $baseRateUSD * (1 + ($markup / 100));

        // 2) Multiplier & minimal dari ENV (pakai juga di server saat store)
        $mult = (float) env('BILLING_MULTIPLIER', 1);   // contoh: 16000 untuk IDR
        $min  = (float) env('BILLING_MIN', 0.01);

        // 3) Rate efektif yang dipakai UI untuk estimasi (HARUS sama dengan server)
        $ratePerThousand = $rateUSDwithMarkup * $mult; // ini yang dipakai JS di view

        return view('orders.create', [
            'service'           => $service,
            'baseRateUSD'       => $baseRateUSD,
            'markup'            => $markup,
            'rateUSDwithMarkup' => $rateUSDwithMarkup,
            'ratePerThousand'   => $ratePerThousand, // <— dipakai JS & tampilan
            'billingMin'        => $min,             // <— batas minimal biaya
            'mult'              => $mult,            // info saja
        ]);
    }


    public function store(Request $request, Service $service)
    {
        $request->validate([
            'link'     => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'integer', 'min:' . $service->min, 'max:' . $service->max],
        ]);

        // Hitung biaya (rate/1000 + markup) + terapkan multiplier & minimal
        $baseRate = (float) $service->rate;                  // harga per 1000 dari provider
        $markup   = (float) ($service->provider->markup_percent ?? 0);
        $rateWithMarkup = $baseRate * (1 + ($markup / 100));

        $qty  = (int) $request->integer('quantity');

        // multiplier & minimal (ENV)
        $mult = (float) (env('BILLING_MULTIPLIER', 1));
        $min  = (float) (env('BILLING_MIN', 0.01));

        $costRaw = $mult * $rateWithMarkup * ($qty / 1000);

        // Bulatkan 2 desimal (karena kolom decimal(12,2)) lalu pastikan ≥ minimal
        $cost = round($costRaw, 2);
        if ($cost < $min) {
            $cost = $min;
        }

        $userId = (int) $request->user()->id;

        // 1) Potong saldo + buat order pending secara atomik (tanpa call HTTP di dalam transaksi)
        try {
            app(\App\Services\WalletService::class)->debit($userId, $cost, [
                'reason'     => 'reserve_for_order',
                'service_id' => $service->id,
                'qty'        => $qty,
                'rate_1000'  => $rateWithMarkup,
            ]);
        } catch (\Throwable $e) {
            // Saldo tidak cukup atau error wallet lainnya
            return back()->withErrors(['quantity' => 'Gagal memproses: ' . $e->getMessage()])->withInput();
        }

        // Buat order lokal (pending)
        $order = \App\Models\Order::create([
            'user_id'           => $userId,
            'service_id'        => $service->id,
            'link'              => $request->string('link'),
            'quantity'          => $qty,
            'status'            => \App\Models\Order::STATUS_PENDING,
            'provider_order_id' => null,
            'cost'              => $cost,
            'meta'              => [
                'base_rate' => $baseRate,
                'markup'    => $markup,
                'rate_1000' => $rateWithMarkup,
            ],
        ]);

        // 2) Kirim ke JAP (di luar transaksi DB). Jika gagal, lakukan refund.
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
                // Tidak ada ID provider -> anggap gagal, REFUND
                app(\App\Services\WalletService::class)->credit($userId, $cost, 'refund', [
                    'reason'   => 'provider_no_id',
                    'order_id' => $order->id,
                ]);

                $order->update([
                    'status' => \App\Models\Order::STATUS_ERROR,
                    'meta'   => array_merge($order->meta ?? [], ['provider_response' => $resp]),
                ]);

                return redirect()->route('dashboard')
                    ->with('status', "Order #{$order->id} gagal di provider (tanpa ID). Saldo dikembalikan.");
            }

            // Sukses
            $order->update([
                'provider_order_id' => (string) $provId,
                'status'            => \App\Models\Order::STATUS_PROCESSING,
                'meta'              => array_merge($order->meta ?? [], ['provider_response' => $resp]),
            ]);

            return redirect()->route('dashboard')
                ->with('status', "Order #{$order->id} dikirim (ID provider: {$provId}). Saldo terpotong Rp " . number_format($cost, 2));
        } catch (\Throwable $e) {
            // Error saat panggil provider -> REFUND
            app(\App\Services\WalletService::class)->credit($userId, $cost, 'refund', [
                'reason'   => 'provider_add_failed',
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            $order->update([
                'status' => \App\Models\Order::STATUS_ERROR,
                'meta'   => array_merge($order->meta ?? [], ['exception' => $e->getMessage()]),
            ]);

            return redirect()->route('dashboard')
                ->with('status', "Order #{$order->id} gagal dikirim ke provider. Saldo dikembalikan. (" . $e->getMessage() . ")");
        }
    }
}
