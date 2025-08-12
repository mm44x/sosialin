<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'         => User::count(),
            'total_wallet'  => (float) Wallet::sum('balance'),
            'orders_total'  => Order::count(),
            'orders_pending' => Order::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
