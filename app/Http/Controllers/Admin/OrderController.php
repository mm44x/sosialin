<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));         // cari id/local, provider_id, link, email
        $status  = trim((string) $request->input('status', ''));    // pending|processing|completed|partial|canceled|error
        $perPage = max(10, min(50, (int) $request->integer('per_page', 20)));

        $rows = Order::query()
            ->with([
                'user:id,name,email',
                'service:id,name,public_name,category_id',
                'service.category:id,name',
            ])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    // Jika angka, izinkan cari tepat ID
                    if (ctype_digit($q)) {
                        $w->orWhere('orders.id', (int) $q);
                    }
                    $w->orWhere('orders.provider_order_id', 'like', "%{$q}%")
                        ->orWhere('orders.link', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('orders.status', $status))
            ->orderByDesc('orders.id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.orders.index', [
            'rows'    => $rows,
            'filters' => ['q' => $q, 'status' => $status, 'per_page' => $perPage],
        ]);
    }

    public function show(Order $order)
    {
        $order->load([
            'user:id,name,email',
            'service.provider:id,name',
            'service.category:id,name',
        ]);

        // timeline dari meta
        $timeline = [];
        $meta = $order->meta ?? [];
        if (!empty($meta['status_history']) && is_array($meta['status_history'])) {
            $timeline = collect($meta['status_history'])
                ->filter(fn($row) => is_array($row))
                ->sortByDesc(fn($row) => $row['at'] ?? '')
                ->values()
                ->all();
        }

        return view('admin.orders.show', [
            'order'    => $order,
            'timeline' => $timeline,
        ]);
    }

    public function statusCheck(Request $request, \App\Models\Order $order)
    {
        // Admin boleh cek semua
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

            $resp = $client->orderStatus(['order' => (string)$order->provider_order_id]);

            $provStatus = strtolower((string)($resp['status'] ?? ''));
            $mapped     = $this->mapProviderStatus($provStatus, $resp);
            $remains    = isset($resp['remains']) ? (float)$resp['remains'] : null;

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
                    'source'  => 'admin_manual',
                ];

                // Perbarui status jika berubah
                if ($locked->status !== $mapped) {
                    $locked->status = $mapped;
                }

                // Refund rules — idempoten lewat flag di meta
                if ($mapped === Order::STATUS_CANCELED) {
                    if (!Arr::get($meta, 'refund_done')) {
                        app(\App\Services\WalletService::class)->credit($locked->user_id, (float)$locked->cost, 'refund', [
                            'reason'   => 'canceled_by_provider_admin',
                            'order_id' => $locked->id,
                        ]);
                        $meta['refund_done'] = true;
                    }
                } elseif ($mapped === Order::STATUS_PARTIAL && $remains !== null) {
                    if (!Arr::get($meta, 'partial_refund_done')) {
                        $qty    = max(1, (float)$locked->quantity);
                        $ratio  = max(0, min(1, $remains / $qty));
                        $refund = round(((float)$locked->cost) * $ratio, 2);
                        if ($refund > 0) {
                            app(\App\Services\WalletService::class)->credit($locked->user_id, $refund, 'refund', [
                                'reason'   => 'partial_refund_admin',
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
            Log::error('ADMIN_ORDER_STATUS_CHECK_ERROR', ['order_id' => $order->id, 'e' => $e->getMessage()]);
            return back()->with('status', "Gagal cek status: " . $e->getMessage());
        }
    }

    /** Salin pemetaan seperti di OrderController */
    private function mapProviderStatus(string $provStatus, array $resp): string
    {
        $provStatus = trim(strtolower($provStatus));
        if (
            $provStatus === 'completed' || $provStatus === 'success' ||
            (($resp['remains'] ?? null) === 0 || ($resp['remains'] ?? null) === '0') // JAP returns '0' for 0 remains
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
        return Order::STATUS_PROCESSING; // Default fallback
    }
}
