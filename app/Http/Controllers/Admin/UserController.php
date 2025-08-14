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

    public function update(Request $request, \App\Models\User $user)
    {
        $me = $request->user();

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'                  => ['required', 'in:user,admin'],
            'is_active'             => ['nullable', 'boolean'],
            'new_password'          => ['nullable', 'string', 'min:8'],
            'new_password_confirm'  => ['nullable', 'same:new_password'],
        ]);

        // Normalisasi
        $isActive = $request->boolean('is_active'); // checkbox
        $role     = $data['role'];

        // Larang ban/demote diri sendiri (supaya tidak terkunci)
        if ((int)$user->id === (int)$me->id) {
            $isActive = true;
            $role     = $user->role; // biarkan tetap
        }

        // Cegah menonaktifkan ATAU mendemote admin terakhir yang masih aktif
        $isAdminNow      = ($user->role === 'admin' && (int)$user->is_active === 1);
        $becomesNotAdmin = ($role !== 'admin' || $isActive === false);

        if ($isAdminNow && $becomesNotAdmin) {
            $otherActiveAdmins = \App\Models\User::where('role', 'admin')
                ->where('is_active', 1)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                return back()
                    ->withErrors(['role' => 'Tidak boleh menonaktifkan atau menurunkan jabatan admin terakhir.'])
                    ->withInput();
            }
        }

        // Update field dasar
        $user->name      = $data['name'];
        $user->email     = $data['email'];
        $user->role      = $role;
        $user->is_active = $isActive ? 1 : 0;

        // Ganti password bila diisi
        if (!empty($data['new_password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'User diperbarui.');
    }

    public function destroy(Request $request, \App\Models\User $user)
    {
        $me = $request->user();

        // Larang hapus diri sendiri
        if ((int)$user->id === (int)$me->id) {
            return back()->with('status', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Cegah hapus admin aktif terakhir
        $isAdminActive = ($user->role === 'admin' && (int)$user->is_active === 1);
        if ($isAdminActive) {
            $otherActiveAdmins = \App\Models\User::where('role', 'admin')
                ->where('is_active', 1)
                ->where('id', '!=', $user->id)
                ->count();

            if ($otherActiveAdmins === 0) {
                return back()->with('status', 'Tidak dapat menghapus admin aktif terakhir.');
            }
        }

        // Jika pakai SoftDeletes, ini akan soft-delete
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User dihapus.');
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
