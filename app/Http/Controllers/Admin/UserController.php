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
        $q        = trim((string) $request->input('q', ''));          // cari ID/Email/Nama
        $sort     = $request->string('sort', 'id_desc')->toString();  // id_desc|id_asc|name_asc|balance_desc|orders_desc
        $roleF    = $request->string('role', '')->toString();         // ''|user|admin
        $statusF  = $request->string('status', '')->toString();       // ''|active|banned
        $perPage  = max(10, min(50, (int) $request->integer('per_page', 20)));

        $builder = \App\Models\User::query()
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
            })
            ->when($roleF !== '', fn($qq) => $qq->where('users.role', $roleF))
            ->when($statusF !== '', function ($qq) use ($statusF) {
                $qq->where('users.is_active', $statusF === 'active' ? 1 : 0);
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
                'q'         => $q,
                'sort'      => $sort,
                'per_page'  => $perPage,
                'role'      => $roleF,
                'status'    => $statusF,
            ],
        ]);
    }

    public function toggleActive(Request $request, \App\Models\User $user)
    {
        $me = $request->user();

        // Tidak boleh ban diri sendiri lewat tombol cepat
        if ((int)$user->id === (int)$me->id) {
            return back()->with('status', 'Tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $targetActive = !$user->is_active; // toggle

        // Jika akan MENONAKTIFKAN admin aktif → jangan jika ini admin aktif terakhir
        if ($user->role === 'admin' && $user->is_active == 1 && $targetActive === false) {
            $otherActiveAdmins = \App\Models\User::where('role', 'admin')
                ->where('is_active', 1)
                ->where('id', '!=', $user->id)
                ->count();
            if ($otherActiveAdmins === 0) {
                return back()->with('status', 'Tidak dapat menonaktifkan admin aktif terakhir.');
            }
        }

        $user->is_active = $targetActive ? 1 : 0;
        $user->save();

        return back()->with('status', $targetActive ? 'User diaktifkan.' : 'User dinonaktifkan (banned).');
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
        $isSelf = (int)$request->user()->id === (int)$user->id;

        // Hitung admin aktif
        $activeAdminCount = \App\Models\User::where('role', 'admin')
            ->where('is_active', 1)
            ->count();

        $isLastActiveAdmin = $user->role === 'admin' && $activeAdminCount <= 1;

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            // BUKAN required: role boleh kosong (mis. select disabled)
            'role'      => ['nullable', 'in:user,admin'],
            // Checkbox boleh tidak ada di payload
            'is_active' => ['sometimes', 'boolean'],
            // Opsional ubah password
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Fallback ke nilai lama bila tidak dikirim
        $role     = $data['role']      ?? $user->role;
        $isActive = array_key_exists('is_active', $data) ? (bool)$data['is_active'] : (bool)$user->is_active;

        // Guard: admin aktif terakhir TIDAK boleh diturunkan atau dibanned
        if ($isLastActiveAdmin) {
            $role     = 'admin';
            $isActive = true;
        }

        // (Opsional) Larang admin menurunkan peran dirinya sendiri
        if ($isSelf && $user->role === 'admin' && $role !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak bisa menurunkan peran admin Anda sendiri.']);
        }

        // Susun payload update
        $update = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $role,
            'is_active' => $isActive,
        ];

        if (!empty($data['password'])) {
            $update['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        $user->update($update);

        // Jika user yang diedit adalah user login saat ini & dibanned, paksa logout via session flag
        if ($user->id === $request->user()->id && $isActive === false) {
            // Tandai agar middleware ForceLogoutIfBanned mengeksekusi logout di request berikutnya
            session()->flash('just_banned_self', true);
        }

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User berhasil diperbarui.');
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
