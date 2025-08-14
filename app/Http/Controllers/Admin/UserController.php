<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));            // cari ID/Email/Nama
        $sort    = $request->string('sort', 'id_desc')->toString();    // id_desc|id_asc|name_asc|balance_desc|orders_desc
        $perPage = max(10, min(50, (int) $request->integer('per_page', 20)));

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
                $builder->orderBy('users.name', 'asc');
                break;
            case 'orders_desc':
                $builder->orderBy('orders_count', 'desc')->orderBy('users.id', 'desc');
                break;
            case 'balance_desc':
                $builder->leftJoin('wallets', 'wallets.user_id', '=', 'users.id')
                    ->orderByRaw('COALESCE(wallets.balance, 0) DESC')
                    ->orderBy('users.id', 'desc')
                    ->select('users.*');
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

    public function show(User $user)
    {
        // saldo & ringkasan
        $user->load(['wallet']);
        $user->loadCount('orders');
        $balance = (float) optional($user->wallet)->balance ?? 0.0;

        // Order terbaru (10)
        $recentOrders = Order::with([
            'service:id,name,public_name,category_id',
            'service.category:id,name',
        ])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Transaksi wallet terbaru (10)
        $recentTx = Transaction::where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.users.show', [
            'user'         => $user,
            'balance'      => $balance,
            'recentOrders' => $recentOrders,
            'recentTx'     => $recentTx,
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'             => ['nullable', 'boolean'], // checkbox
        ]);

        $payload = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'is_active' => $request->boolean('is_active'), // unchecked => false (banned)
        ];

        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index', $user)
            ->with('status', 'User berhasil diperbarui' . ($payload['is_active'] ? '' : ' (Nonaktif/Banned)') . '.');
    }

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
                $builder->orderBy('users.name', 'asc');
                break;
            case 'orders_desc':
                $builder->orderBy('orders_count', 'desc')->orderBy('users.id', 'desc');
                break;
            case 'balance_desc':
                $builder->leftJoin('wallets', 'wallets.user_id', '=', 'users.id')
                    ->orderByRaw('COALESCE(wallets.balance, 0) DESC')
                    ->orderBy('users.id', 'desc')
                    ->select('users.*');
                break;
            case 'id_desc':
            default:
                $builder->orderBy('users.id', 'desc');
                break;
        }

        $rows = $builder->limit(20000)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

            fputcsv($out, [
                'User ID',
                'Email',
                'Name',
                'Is Active',
                'Balance (IDR)',
                'Orders Count',
                'Created At',
                'Updated At',
            ]);

            foreach ($rows as $u) {
                $balance = (float) optional($u->wallet)->balance ?? 0;
                fputcsv($out, [
                    $u->id,
                    $u->email,
                    $u->name,
                    (int) ($u->is_active ? 1 : 0),
                    number_format($balance, 2, '.', ''),
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
