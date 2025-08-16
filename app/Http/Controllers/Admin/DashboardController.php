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

        // Data untuk chart orders trend (7 hari terakhir)
        $ordersTrend = $this->getOrdersTrendData();

        // Data untuk chart revenue (7 hari terakhir)
        $revenueTrend = $this->getRevenueTrendData();

        // 10 order terbaru
        $recentOrders = Order::with(['user:id,name,email', 'service:id,name', 'service.category:id,name'])
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
            'ordersTrend'   => $ordersTrend,
            'revenueTrend'  => $revenueTrend,
            'recentOrders'  => $recentOrders,
            'recentApiLogs' => $recentApiLogs,
        ]);
    }

    /**
     * Get orders trend data for the last 7 days
     */
    private function getOrdersTrendData()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            
            $count = Order::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'count' => $count,
                'formatted' => number_format($count)
            ];
        }
        
        return $data;
    }

    /**
     * Get revenue trend data for the last 7 days
     */
    private function getRevenueTrendData()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            
            // Revenue dari orders yang completed
            $revenue = Order::where('status', Order::STATUS_COMPLETED)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('cost');
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'amount' => (float) $revenue,
                'formatted' => 'Rp ' . number_format($revenue, 0, ',', '.')
            ];
        }
        
        return $data;
    }
}
