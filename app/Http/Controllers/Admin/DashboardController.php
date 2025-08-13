<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Order;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'users_total'        => User::count(),
            'wallet_total'       => (float) Wallet::sum('balance'),
            'orders_pending_now' => Order::whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING
            ])->count(),
            'orders_today'       => Order::whereDate('created_at', $today)->count(),
            'revenue_today'      => (float) Order::whereIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_PARTIAL
            ])->whereDate('created_at', $today)->sum('cost'),
            'completed_24h'      => Order::where('status', Order::STATUS_COMPLETED)
                ->where('updated_at', '>=', now()->subDay())->count(),
        ];

        $latestOrders = Order::with(['service', 'user'])
            ->orderByDesc('id')->limit(8)->get();

        $pendingOrders = Order::with(['service', 'user'])
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
            ->orderBy('updated_at', 'desc')->limit(8)->get();

        $latestUsers = User::orderByDesc('id')->limit(8)->get(['id', 'name', 'email', 'created_at']);

        return view('admin.dashboard', compact('stats', 'latestOrders', 'pendingOrders', 'latestUsers'));
    }
}
