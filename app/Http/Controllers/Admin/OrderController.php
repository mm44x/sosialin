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
}
