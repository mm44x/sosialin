<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\OrderSubmitJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = \App\Models\Order::with(['service.provider', 'service.category'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Request $request, \App\Models\Order $order)
    {
        abort_if($order->user_id !== $request->user()->id, 403);
        $order->load(['service.provider', 'service.category']);
        return view('orders.show', ['order' => $order]);
    }

    public function statusCheck(Request $request, \App\Models\Order $order)
    {
        // Pemilik order atau admin
        $user = $request->user();
        if ($user->role !== 'admin' && (int)$order->user_id !== (int)$user->id) {
            abort(403);
        }

        if (!$order->provider_order_id) {
            return back()->with('status', 'Order belum memiliki ID provider — belum dapat dicek.');
        }

        try {
            // Ambil service + provider (robust)
            $service  = $order->service()->with('provider')->firstOrFail();
            $provider = $service->provider;

            $client = new \App\Services\Smm\JapClient(
                baseUrl: env('JAP_BASE_URL'),
                apiKey: env('JAP_API_KEY'),
                providerId: $provider->id
            );

            $resp = $client->orderStatus(['order' => (string)$order->provider_order_id]);

            $provStatus = strtolower((string)($resp['status'] ?? ''));
            $mapped     = $this->mapProviderStatus($provStatus, $resp);
            $remains    = isset($resp['remains']) ? (float)$resp['remains'] : null;

            // Tulis perubahan + refund secara atomik & idempoten
            DB::transaction(function () use ($order, $mapped, $resp, $remains) {
                /** @var \App\Models\Order $locked */
                $locked = \App\Models\Order::where('id', $order->id)->lockForUpdate()->first();

                $meta = $locked->meta ?? [];
                $meta['last_status_response'] = $resp;
                $meta['status_history'][] = [
                    'at'      => now()->toDateTimeString(),
                    'status'  => strtolower((string)($resp['status'] ?? '')),
                    'mapped'  => $mapped,
                    'remains' => $remains,
                ];

                // Update status jika berubah
                if ($locked->status !== $mapped) {
                    $locked->status = $mapped;
                }

                // Refund rules — SEKALI saja
                if ($mapped === \App\Models\Order::STATUS_CANCELED) {
                    if (!Arr::get($meta, 'refund_done')) {
                        app(\App\Services\WalletService::class)->credit($locked->user_id, (float)$locked->cost, 'refund', [
                            'reason'   => 'canceled_by_provider_manual_check',
                            'order_id' => $locked->id,
                        ]);
                        $meta['refund_done'] = true;
                    }
                } elseif ($mapped === \App\Models\Order::STATUS_PARTIAL && $remains !== null) {
                    if (!Arr::get($meta, 'partial_refund_done')) {
                        $qty    = max(1, (float)$locked->quantity);
                        $ratio  = max(0, min(1, $remains / $qty)); // 0..1
                        $refund = round(((float)$locked->cost) * $ratio, 2);
                        if ($refund > 0) {
                            app(\App\Services\WalletService::class)->credit($locked->user_id, $refund, 'refund', [
                                'reason'   => 'partial_refund_manual_check',
                                'order_id' => $locked->id,
                                'remains'  => $remains,
                                'ratio'    => $ratio,
                            ]);
                            $meta['partial_refund_done']   = true;
                            $meta['partial_refund_amount'] = $refund;
                        }
                    }
                }

                $locked->meta = $meta;
                $locked->save();
            });

            return back()->with('status', "Status diperbarui: " . ucfirst($mapped));
        } catch (\Throwable $e) {
            Log::error('ORDER_STATUS_CHECK_ERROR', ['order_id' => $order->id, 'e' => $e->getMessage()]);
            return back()->with('status', "Gagal cek status: " . $e->getMessage());
        }
    }

    /** Pemetaan status provider → status internal. */
    private function mapProviderStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim(strtolower($provStatus));
        if (
            $provStatus === 'completed' || $provStatus === 'success' ||
            (($resp['remains'] ?? null) === 0 || ($resp['remains'] ?? null) === '0')
        ) {
            return \App\Models\Order::STATUS_COMPLETED;
        }
        if ($provStatus === 'partial') {
            return \App\Models\Order::STATUS_PARTIAL;
        }
        if ($provStatus === 'canceled' || $provStatus === 'cancelled') {
            return \App\Models\Order::STATUS_CANCELED;
        }
        if ($provStatus === 'processing' || $provStatus === 'in progress' || $provStatus === 'inprogress') {
            return \App\Models\Order::STATUS_PROCESSING;
        }
        if ($provStatus === 'pending' || $provStatus === '') {
            return \App\Models\Order::STATUS_PENDING;
        }
        return \App\Models\Order::STATUS_PROCESSING;
    }


    // public function refreshStatus(Request $request, \App\Models\Order $order)
    // {
    //     abort_if($order->user_id !== $request->user()->id, 403);

    //     if (!$order->provider_order_id) {
    //         return back()->with('status', 'Order belum terkirim ke provider atau tidak punya provider_order_id.');
    //     }

    //     try {
    //         $client = new \App\Services\Smm\JapClient(
    //             baseUrl: env('JAP_BASE_URL'),
    //             apiKey: env('JAP_API_KEY'),
    //             providerId: $order->service->provider_id
    //         );

    //         $resp = $client->orderStatus(['order' => (string)$order->provider_order_id]);

    //         $provStatus = strtolower((string)($resp['status'] ?? ''));
    //         $mapped = $this->mapStatus($provStatus, $resp);

    //         $meta = $order->meta ?? [];
    //         $meta['last_status_response'] = $resp;
    //         $meta['status_history'][] = [
    //             'at'     => now()->toDateTimeString(),
    //             'status' => $provStatus,
    //             'remains' => $resp['remains'] ?? null,
    //         ];

    //         $order->update([
    //             'status' => $mapped,
    //             'meta'   => $meta,
    //         ]);

    //         return back()->with('status', "Status terbaru dari provider: {$provStatus} → {$mapped}");
    //     } catch (\Throwable $e) {
    //         return back()->with('status', 'Gagal cek status: ' . $e->getMessage());
    //     }
    // }

    /** Map status provider ke status lokal */
    protected function mapStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim($provStatus);

        if ($provStatus === 'completed' || $provStatus === 'success' || ($resp['remains'] ?? null) === 0) {
            return \App\Models\Order::STATUS_COMPLETED;
        }
        if ($provStatus === 'partial') {
            return \App\Models\Order::STATUS_PARTIAL;
        }
        if ($provStatus === 'canceled' || $provStatus === 'cancelled') {
            return \App\Models\Order::STATUS_CANCELED;
        }
        if ($provStatus === 'processing' || $provStatus === 'in progress' || $provStatus === 'inprogress') {
            return \App\Models\Order::STATUS_PROCESSING;
        }
        if ($provStatus === 'pending' || $provStatus === '') {
            return \App\Models\Order::STATUS_PENDING;
        }
        return \App\Models\Order::STATUS_PROCESSING;
    }

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
            'link'     => ['required', 'string', 'max:500', 'starts_with:http://,https://'],
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
