<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // <— tambahkan

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $status  = trim((string) $request->input('status', ''));
        $perPage = max(10, min(50, (int) $request->integer('per_page', 20)));

        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        // ⇨ jika user kebalik isi tanggal, otomatis ditukar
        if ($dateFrom && $dateTo && Carbon::parse($dateFrom)->gt(Carbon::parse($dateTo))) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $rows = Order::query()
            ->with(['user:id,name,email', 'service:id,name,public_name,category_id', 'service.category:id,name'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) $w->orWhere('orders.id', (int) $q);
                    $w->orWhere('orders.provider_order_id', 'like', "%{$q}%")
                        ->orWhere('orders.link', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('orders.status', $status))
            ->when($dateFrom, fn($qq) => $qq->where('orders.created_at', '>=', Carbon::parse($dateFrom)->startOfDay()))
            ->when($dateTo,   fn($qq) => $qq->where('orders.created_at', '<=', Carbon::parse($dateTo)->endOfDay()))
            ->orderByDesc('orders.id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.orders.index', [
            'rows'    => $rows,
            'filters' => [
                'q'        => $q,
                'status'   => $status,
                'per_page' => $perPage,
                'date_from' => $dateFrom,
                'date_to'  => $dateTo,
            ],
        ]);
    }


    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $q = Order::query()
            ->with(['user:id,name,email', 'service:id,name,public_name', 'service.category:id,name'])
            ->when($request->filled('q'), function ($qq) use ($request) {
                $s = trim((string) $request->input('q'));
                $qq->where(function ($w) use ($s) {
                    $w->where('id', $s)
                        ->orWhere('provider_order_id', 'like', "%{$s}%")
                        ->orWhere('link', 'like', "%{$s}%")
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$s}%")
                            ->orWhere('name', 'like', "%{$s}%"))
                        ->orWhereHas('service', fn($sv) => $sv->where('name', 'like', "%{$s}%")
                            ->orWhere('public_name', 'like', "%{$s}%"));
                });
            })
            ->when($request->filled('status'), fn($qq) => $qq->where('status', strtolower((string) $request->input('status'))))
            ->when($dateFrom, fn($qq) => $qq->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay()))
            ->when($dateTo,   fn($qq) => $qq->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay()))
            ->orderByDesc('id');

        $rows = $q->limit(20000)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="orders_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Order ID', 'User Email', 'User Name', 'Service', 'Category', 'Qty', 'Cost', 'Status', 'Provider Order ID', 'Link', 'Created At', 'Updated At']);
            foreach ($rows as $o) {
                fputcsv($out, [
                    $o->id,
                    $o->user->email ?? '',
                    $o->user->name ?? '',
                    $o->service->public_name ?? $o->service->name ?? '',
                    $o->service->category->name ?? '',
                    $o->quantity,
                    number_format((float) $o->cost, 2, '.', ''),
                    $o->status,
                    $o->provider_order_id,
                    $o->link,
                    optional($o->created_at)->toDateTimeString(),
                    optional($o->updated_at)->toDateTimeString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
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

    public function bulkStatusCheck(Request $request)
    {
        // Ambil IDs dari "ids" (CSV atau array)
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }
        $ids = array_values(array_unique(array_map('intval', (array)$ids)));

        if (empty($ids)) {
            return back()->with('status', 'Tidak ada order yang dipilih.');
        }

        $orders = \App\Models\Order::with(['service.provider'])->whereIn('id', $ids)->get();

        $stats = [
            'total'        => count($ids),
            'processed'    => 0,
            'unchanged'    => 0,
            'noProvId'     => 0,
            'refunded'     => 0,
            'partial'      => 0,
            'skippedFinal' => 0, // completed / error
            'errors'       => 0,
        ];

        foreach ($orders as $order) {
            try {
                // SKIP kalau status final
                if (in_array($order->status, [
                    \App\Models\Order::STATUS_COMPLETED,
                    \App\Models\Order::STATUS_ERROR,
                ], true)) {
                    $stats['skippedFinal']++;
                    continue;
                }

                $service  = $order->service;
                $provider = $service?->provider;

                if (!$order->provider_order_id || !$provider) {
                    $stats['noProvId']++;
                    continue;
                }

                $baseUrl = $provider->base_url ?: env('JAP_BASE_URL');
                $apiKey  = $provider->api_key  ?: env('JAP_API_KEY');

                $client = new \App\Services\Smm\JapClient(
                    baseUrl: $baseUrl,
                    apiKey: $apiKey,
                    providerId: $provider->id
                );

                $resp       = $client->orderStatus(['order' => (string) $order->provider_order_id]);
                $provStatus = strtolower((string)($resp['status'] ?? ''));
                $mapped     = $this->mapProviderStatus($provStatus, $resp);
                $remains    = isset($resp['remains']) ? (float)$resp['remains'] : null;

                \Illuminate\Support\Facades\DB::transaction(function () use ($order, $mapped, $resp, $remains, &$stats) {
                    /** @var \App\Models\Order $locked */
                    $locked = \App\Models\Order::where('id', $order->id)->lockForUpdate()->first();
                    if (!$locked) return;

                    // Safety: SKIP kalau mendadak final saat transaksi dimulai
                    if (in_array($locked->status, [
                        \App\Models\Order::STATUS_COMPLETED,
                        \App\Models\Order::STATUS_ERROR,
                    ], true)) {
                        $stats['skippedFinal']++;
                        return;
                    }

                    $meta = $locked->meta ?? [];
                    $meta['last_status_response'] = $resp;
                    $meta['status_history'][] = [
                        'at'      => now()->toDateTimeString(),
                        'status'  => strtolower((string)($resp['status'] ?? '')),
                        'mapped'  => $mapped,
                        'remains' => $remains,
                        'source'  => 'admin_bulk',
                    ];

                    $changed = false;
                    if ($locked->status !== $mapped) {
                        $locked->status = $mapped;
                        $changed = true;
                    }

                    // Refund rules (idempoten via flag di meta)
                    if ($mapped === \App\Models\Order::STATUS_CANCELED) {
                        if (!\Illuminate\Support\Arr::get($meta, 'refund_done')) {
                            app(\App\Services\WalletService::class)->credit($locked->user_id, (float)$locked->cost, 'refund', [
                                'reason'   => 'canceled_by_provider_bulk',
                                'order_id' => $locked->id,
                            ]);
                            $meta['refund_done'] = true;
                            $stats['refunded']++;
                            $changed = true;
                        }
                    } elseif ($mapped === \App\Models\Order::STATUS_PARTIAL && $remains !== null) {
                        if (!\Illuminate\Support\Arr::get($meta, 'partial_refund_done')) {
                            $qty    = max(1, (float)$locked->quantity);
                            $ratio  = max(0, min(1, $remains / $qty));
                            $refund = round(((float)$locked->cost) * $ratio, 2);
                            if ($refund > 0) {
                                app(\App\Services\WalletService::class)->credit($locked->user_id, $refund, 'refund', [
                                    'reason'   => 'partial_refund_bulk',
                                    'order_id' => $locked->id,
                                    'remains'  => $remains,
                                    'ratio'    => $ratio,
                                ]);
                                $meta['partial_refund_done']   = true;
                                $meta['partial_refund_amount'] = $refund;
                                $stats['partial']++;
                                $changed = true;
                            }
                        }
                    }

                    $locked->meta = $meta;
                    $locked->save();

                    if ($changed) $stats['processed']++;
                    else $stats['unchanged']++;
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('ADMIN_BULK_STATUS_ERROR', [
                    'order_id' => $order->id ?? null,
                    'e'        => $e->getMessage(),
                ]);
                $stats['errors']++;
            }
        }

        $msg = "Bulk cek: total {$stats['total']}, diproses {$stats['processed']}, tidak berubah {$stats['unchanged']}, " .
            "tanpa Prov.ID {$stats['noProvId']}, partial refund {$stats['partial']}, " .
            "skip final {$stats['skippedFinal']}, gagal {$stats['errors']}.";
        return back()->with('status', $msg);
    }
}
