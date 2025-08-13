<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil string dari query (?from=YYYY-MM-DD&to=YYYY-MM-DD)
        $fromStr = $request->input('from');
        $toStr   = $request->input('to');

        // Parse aman + fallback ke hari ini
        try {
            $from = $fromStr ? Carbon::parse($fromStr) : Carbon::today();
        } catch (\Throwable $e) {
            $from = Carbon::today();
        }
        try {
            $to = $toStr ? Carbon::parse($toStr) : Carbon::today();
        } catch (\Throwable $e) {
            $to = Carbon::today();
        }

        // Jika to < from, tukar supaya valid
        if ($to->lessThan($from)) {
            [$from, $to] = [$to, $from];
        }

        // Rentang waktu inklusif
        $fromStart = $from->copy()->startOfDay();
        $toEnd     = $to->copy()->endOfDay();

        // KPI utama
        $totalUsers = User::count();
        $totalSaldo = (float) Wallet::sum('balance');

        $ordersPending    = Order::where('status', Order::STATUS_PENDING)->count();
        $ordersProcessing = Order::where('status', Order::STATUS_PROCESSING)->count();
        $ordersCompleted  = Order::where('status', Order::STATUS_COMPLETED)->count();
        $ordersError      = Order::whereIn('status', [
            Order::STATUS_CANCELED,
            Order::STATUS_ERROR,
            Order::STATUS_PARTIAL
        ])->count();

        // Aggregat transaksi pada rentang tanggal
        $topupSum = (float) Transaction::where('type', 'topup')
            ->whereBetween('created_at', [$fromStart, $toEnd])
            ->sum('amount');

        $orderDebitSum = (float) Transaction::where('type', 'order')
            ->whereBetween('created_at', [$fromStart, $toEnd])
            ->sum('amount'); // biasanya negatif

        $refundSum = (float) Transaction::where('type', 'refund')
            ->whereBetween('created_at', [$fromStart, $toEnd])
            ->sum('amount');

        // 10 order terbaru
        $recentOrders = Order::with(['user:id,name', 'service:id,name'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // 10 API log terbaru
        $recentApiLogs = ApiLog::with('provider:id,name')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'range' => [
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
            ],
            'kpi' => [
                'totalUsers'    => $totalUsers,
                'totalSaldo'    => $totalSaldo,
                'pending'       => $ordersPending,
                'processing'    => $ordersProcessing,
                'completed'     => $ordersCompleted,
                'error'         => $ordersError,
                'topupSum'      => $topupSum,
                'orderDebitSum' => $orderDebitSum,
                'refundSum'     => $refundSum,
            ],
            'recentOrders'  => $recentOrders,
            'recentApiLogs' => $recentApiLogs,
        ]);
    }
}
