<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List users dengan filter, sorting, dan pagination.
     */
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));                 // cari ID / email / nama
        $sort    = $request->string('sort', 'id_desc')->toString();         // id_desc|id_asc|name_asc|balance_desc|orders_desc
        $perPage = max(10, min(50, (int) $request->integer('per_page', 20)));

        // Base query
        $builder = User::query()
            ->select('users.*')
            ->with(['wallet:id,user_id,balance']) // eager load 1:1
            ->withCount('orders')                  // ->orders_count
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('users.id', (int) $q);
                    }
                    $w->orWhere('users.email', 'like', "%{$q}%")
                        ->orWhere('users.name',  'like', "%{$q}%");
                });
            });

        // Sorting
        switch ($sort) {
            case 'id_asc':
                $builder->orderBy('users.id', 'asc');
                break;

            case 'name_asc':
                $builder->orderBy('users.name', 'asc')->orderBy('users.id', 'asc');
                break;

            case 'orders_desc':
                $builder->orderBy('orders_count', 'desc')->orderBy('users.id', 'desc');
                break;

            case 'balance_desc':
                // Join ringan untuk sorting berdasar saldo (fallback 0 bila null)
                $builder->leftJoin('wallets as w', 'w.user_id', '=', 'users.id')
                    ->orderByRaw('COALESCE(w.balance, 0) DESC')
                    ->orderBy('users.id', 'desc')
                    ->select('users.*'); // penting agar model tetap utuh
                break;

            case 'id_desc':
            default:
                $builder->orderBy('users.id', 'desc');
                break;
        }

        $rows = $builder->paginate($perPage)->withQueryString();

        return view('admin.users.index', [
            'rows'    => $rows,
            'filters' => [
                'q'        => $q,
                'sort'     => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Export CSV mengikuti filter & sorting dari index.
     */
    public function export(Request $request)
    {
        $q    = trim((string) $request->input('q', ''));
        $sort = $request->string('sort', 'id_desc')->toString();

        $builder = User::query()
            ->select('users.*')
            ->with(['wallet:id,user_id,balance'])
            ->withCount('orders')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('users.id', (int) $q);
                    }
                    $w->orWhere('users.email', 'like', "%{$q}%")
                        ->orWhere('users.name',  'like', "%{$q}%");
                });
            });

        switch ($sort) {
            case 'id_asc':
                $builder->orderBy('users.id', 'asc');
                break;

            case 'name_asc':
                $builder->orderBy('users.name', 'asc')->orderBy('users.id', 'asc');
                break;

            case 'orders_desc':
                $builder->orderBy('orders_count', 'desc')->orderBy('users.id', 'desc');
                break;

            case 'balance_desc':
                $builder->leftJoin('wallets as w', 'w.user_id', '=', 'users.id')
                    ->orderByRaw('COALESCE(w.balance, 0) DESC')
                    ->orderBy('users.id', 'desc')
                    ->select('users.*');
                break;

            case 'id_desc':
            default:
                $builder->orderBy('users.id', 'desc');
                break;
        }

        // Batasi output agar ringan
        $rows = $builder->limit(20000)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 agar nyaman di Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, [
                'User ID',
                'Email',
                'Name',
                'Balance (IDR)',
                'Orders Count',
                'Created At',
                'Updated At',
            ]);

            foreach ($rows as $u) {
                $balance = (float) ((optional($u->wallet)->balance) ?? 0);
                fputcsv($out, [
                    $u->id,
                    $u->email,
                    $u->name,
                    number_format($balance, 2, '.', ''), // numeric-friendly
                    (int) ($u->orders_count ?? 0),
                    optional($u->created_at)->toDateTimeString(),
                    optional($u->updated_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
