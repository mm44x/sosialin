<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->input('q', '')); // cari id, user email/nama
        $type     = trim((string) $request->input('type', '')); // topup|order|refund
        $minAmt   = $request->filled('min') ? (float) $request->input('min') : null;
        $maxAmt   = $request->filled('max') ? (float) $request->input('max') : null;
        $dateFrom = $request->input('from'); // YYYY-MM-DD
        $dateTo   = $request->input('to');   // YYYY-MM-DD
        $perPage  = max(10, min(50, (int) $request->integer('per_page', 20)));

        $rows = Transaction::query()
            ->with(['user:id,name,email'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('transactions.id', (int) $q);
                    }
                    $w->orWhereHas('user', function ($u) use ($q) {
                        $u->where('email', 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%");
                    });
                });
            })
            ->when($type !== '', fn($qq) => $qq->where('type', $type))
            ->when(!is_null($minAmt), fn($qq) => $qq->where('amount', '>=', $minAmt))
            ->when(!is_null($maxAmt), fn($qq) => $qq->where('amount', '<=', $maxAmt))
            ->when($dateFrom, fn($qq) => $qq->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($qq) => $qq->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.transactions.index', [
            'rows'    => $rows,
            'filters' => [
                'q'        => $q,
                'type'     => $type,
                'min'      => $minAmt,
                'max'      => $maxAmt,
                'from'     => $dateFrom,
                'to'       => $dateTo,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function export(Request $request)
    {
        // Duplikasi filter dari index
        $q        = trim((string) $request->input('q', ''));
        $type     = trim((string) $request->input('type', ''));
        $minAmt   = $request->filled('min') ? (float) $request->input('min') : null;
        $maxAmt   = $request->filled('max') ? (float) $request->input('max') : null;
        $dateFrom = $request->input('from');
        $dateTo   = $request->input('to');

        $query = Transaction::query()
            ->with(['user:id,name,email'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('transactions.id', (int) $q);
                    }
                    $w->orWhereHas('user', function ($u) use ($q) {
                        $u->where('email', 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%");
                    });
                });
            })
            ->when($type !== '', fn($qq) => $qq->where('type', $type))
            ->when(!is_null($minAmt), fn($qq) => $qq->where('amount', '>=', $minAmt))
            ->when(!is_null($maxAmt), fn($qq) => $qq->where('amount', '<=', $maxAmt))
            ->when($dateFrom, fn($qq) => $qq->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($qq) => $qq->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('id');

        $rows = $query->limit(50000)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transactions_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM agar Excel nyaman
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, [
                'ID',
                'User Email',
                'User Name',
                'Type',
                'Amount',
                'Meta(JSON)',
                'Created At',
            ]);

            foreach ($rows as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->user->email ?? '',
                    $t->user->name ?? '',
                    $t->type,
                    number_format((float)$t->amount, 2, '.', ''),
                    json_encode($t->meta ?? [], JSON_UNESCAPED_SLASHES),
                    optional($t->created_at)->toDateTimeString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
