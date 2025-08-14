<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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
}
