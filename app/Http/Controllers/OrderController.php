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
        $userId = (int) $request->user()->id;

        $q = \App\Models\Order::query()
            ->with(['service.category']) // jangan expose provider di user
            ->where('user_id', $userId);

        // Cari: ID order, provider_order_id, link, atau nama layanan
        if ($request->filled('search')) {
            $s = trim($request->string('search')->toString());
            $q->where(function ($w) use ($s) {
                $w->where('id', (int) $s) // cocokkan angka jadi exact untuk ID
                    ->orWhere('provider_order_id', 'like', "%{$s}%")
                    ->orWhere('link', 'like', "%{$s}%")
                    ->orWhereHas('service', fn($qq) => $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('public_name', 'like', "%{$s}%"));
            });
        }

        // Filter status (aman)
        $allowed = ['pending', 'processing', 'completed', 'partial', 'canceled', 'error'];
        if ($request->filled('status')) {
            $st = strtolower($request->string('status')->toString());
            if (in_array($st, $allowed, true)) {
                $q->where('status', $st);
            }
        }

        $orders = $q->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', [
            'orders'  => $orders,
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        // Pastikan hanya pemilik yang bisa melihat
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load(['service.category', 'service.provider']);

        // Ambil timeline dari meta (array)
        $timeline = [];
        $meta = $order->meta ?? [];
        if (!empty($meta['status_history']) && is_array($meta['status_history'])) {
            // urutkan dari terbaru ke lama
            $timeline = collect($meta['status_history'])
                ->filter(fn($row) => is_array($row))
                ->sortByDesc(fn($row) => $row['at'] ?? '')
                ->values()
                ->all();
        }

        return view('orders.show', [
            'order'    => $order,
            'timeline' => $timeline,
        ]);
    }

    public function statusCheck(Request $request, Order $order)
    {
        // Pemilik order atau admin
        $user = $request->user();
        if ($user->role !== 'admin' && (int) $order->user_id !== (int) $user->id) {
            abort(403);
        }

        if (!$order->provider_order_id) {
            return back()->with('status', 'Order belum memiliki ID provider — belum dapat dicek.');
        }

        try {
            $service  = $order->service()->with('provider')->firstOrFail();
            $provider = $service->provider;

            $baseUrl = $provider->base_url ?: env('JAP_BASE_URL');
            $apiKey  = $provider->api_key  ?: env('JAP_API_KEY');

            $client = new \App\Services\Smm\JapClient(
                baseUrl: $baseUrl,
                apiKey: $apiKey,
                providerId: $provider->id
            );

            $resp = $client->orderStatus(['order' => (string) $order->provider_order_id]);

            $provStatus = strtolower((string) ($resp['status'] ?? ''));
            $mapped     = $this->mapProviderStatus($provStatus, $resp);
            $remains    = isset($resp['remains']) ? (float) $resp['remains'] : null;

            // Tulis perubahan + refund secara atomik & idempoten
            DB::transaction(function () use ($order, $mapped, $resp, $remains) {
                /** @var \App\Models\Order $locked */
                $locked = Order::where('id', $order->id)->lockForUpdate()->first();

                $meta = $locked->meta ?? [];
                $meta['last_status_response'] = $resp;
                $meta['status_history'][] = [
                    'at'      => now()->toDateTimeString(),
                    'status'  => strtolower((string) ($resp['status'] ?? '')),
                    'mapped'  => $mapped,
                    'remains' => $remains,
                ];

                // Update status jika berubah
                if ($locked->status !== $mapped) {
                    $locked->status = $mapped;
                }

                // Refund rules — SEKALI saja
                if ($mapped === Order::STATUS_CANCELED) {
                    if (!Arr::get($meta, 'refund_done')) {
                        app(\App\Services\WalletService::class)->credit($locked->user_id, (float) $locked->cost, 'refund', [
                            'reason'   => 'canceled_by_provider_manual_check',
                            'order_id' => $locked->id,
                        ]);
                        $meta['refund_done'] = true;
                    }
                } elseif ($mapped === Order::STATUS_PARTIAL && $remains !== null) {
                    if (!Arr::get($meta, 'partial_refund_done')) {
                        $qty    = max(1, (float) $locked->quantity);
                        $ratio  = max(0, min(1, $remains / $qty)); // 0..1
                        $refund = round(((float) $locked->cost) * $ratio, 2);
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

            return back()->with('status', 'Status diperbarui: ' . ucfirst($mapped));
        } catch (\Throwable $e) {
            Log::error('ORDER_STATUS_CHECK_ERROR', ['order_id' => $order->id, 'e' => $e->getMessage()]);
            return back()->with('status', 'Gagal cek status: ' . $e->getMessage());
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
            return Order::STATUS_COMPLETED;
        }
        if ($provStatus === 'partial') {
            return Order::STATUS_PARTIAL;
        }
        if ($provStatus === 'canceled' || $provStatus === 'cancelled') {
            return Order::STATUS_CANCELED;
        }
        if ($provStatus === 'processing' || $provStatus === 'in progress' || $provStatus === 'inprogress') {
            return Order::STATUS_PROCESSING;
        }
        if ($provStatus === 'pending' || $provStatus === '') {
            return Order::STATUS_PENDING;
        }
        return Order::STATUS_PROCESSING;
    }

    // (Cadangan lama – tidak dipakai, tetapi dibiarkan jika ada referensi lain)
    protected function mapStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim($provStatus);

        if ($provStatus === 'completed' || $provStatus === 'success' || ($resp['remains'] ?? null) === 0) {
            return Order::STATUS_COMPLETED;
        }
        if ($provStatus === 'partial') {
            return Order::STATUS_PARTIAL;
        }
        if ($provStatus === 'canceled' || $provStatus === 'cancelled') {
            return Order::STATUS_CANCELED;
        }
        if ($provStatus === 'processing' || $provStatus === 'in progress' || $provStatus === 'inprogress') {
            return Order::STATUS_PROCESSING;
        }
        if ($provStatus === 'pending' || $provStatus === '') {
            return Order::STATUS_PENDING;
        }
        return Order::STATUS_PROCESSING;
    }

    public function create(Service $service)
    {
        // Guard: hanya layanan aktif & public
        if (!$service->active || !$service->public_active) {
            abort(404); // samarkan agar tidak bisa diintip
        }

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

        // Guard: blokir order jika layanan tidak aktif/tidak publik
        if (!$service->active || !$service->public_active) {
            return back()->withErrors(['quantity' => 'Layanan tidak tersedia.'])->withInput();
        }

        $qty = (int) $request->integer('quantity');

        // === Satu-satunya sumber kebenaran harga ===
        $ps = app(\App\Services\PricingService::class);
        $bd = $ps->breakdown($service, $qty);

        $cost           = $bd['cost'];               // FINAL — gunakan ini saja
        $baseRate       = $bd['baseRateUSD'];
        $markup         = $bd['usedMarkup'];
        $rateWithMarkup = $bd['rateUSDwithMarkup'];  // USD / 1000 (after markup)

        $userId = (int) $request->user()->id;

        // 1) Potong saldo + buat order pending secara atomik (tanpa call HTTP di dalam transaksi)
        try {
            app(\App\Services\WalletService::class)->debit($userId, $cost, [
                'reason'           => 'reserve_for_order',
                'service_id'       => $service->id,
                'qty'              => $qty,
                'rate_1000_usd'    => $rateWithMarkup,
                'rate_1000_local'  => $bd['ratePerThousandLocal'],
            ]);
        } catch (\Throwable $e) {
            // Saldo tidak cukup atau error wallet lainnya
            return back()->withErrors(['quantity' => 'Gagal memproses: ' . $e->getMessage()])->withInput();
        }

        // Buat order lokal (pending)
        $order = Order::create([
            'user_id'    => $userId,
            'service_id' => $service->id,
            'link'       => (string) $request->input('link'),
            'quantity'   => $qty,
            'status'     => Order::STATUS_PENDING,
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
