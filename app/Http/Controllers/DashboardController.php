<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Data wallet dan saldo
        $wallet = $user->wallet;
        $balance = $wallet ? $wallet->balance : 0;
        
        // Statistik pesanan
        $orderStats = $this->getOrderStats($user->id);
        
        // Statistik transaksi
        $transactionStats = $this->getTransactionStats($user->id);
        
        // Pesanan terbaru
        $recentOrders = $this->getRecentOrders($user->id);
        
        // Transaksi terbaru
        $recentTransactions = $this->getRecentTransactions($user->id);
        
        // Ticket terbaru
        $recentTickets = $this->getRecentTickets($user->id);
        
        // Layanan populer
        $popularServices = $this->getPopularServices();
        
        // Kategori layanan
        $categories = Category::withCount(['services' => function($query) {
            $query->where('active', true)->where('public_active', true);
        }])->orderBy('services_count', 'desc')->limit(6)->get();
        
        // Chart data untuk 7 hari terakhir
        $chartData = $this->getChartData($user->id);
        
        // Notifikasi dan alert
        $notifications = $this->getNotifications($user);
        
        // Quick stats
        $quickStats = [
            'total_spent' => $this->getTotalSpent($user->id),
            'avg_order_value' => $this->getAverageOrderValue($user->id),
            'success_rate' => $this->getSuccessRate($user->id),
            'days_since_last_order' => $this->getDaysSinceLastOrder($user->id),
        ];

        return view('dashboard', compact(
            'balance',
            'orderStats',
            'transactionStats', 
            'recentOrders',
            'recentTransactions',
            'recentTickets',
            'popularServices',
            'categories',
            'chartData',
            'notifications',
            'quickStats'
        ));
    }

    private function getOrderStats($userId)
    {
        $orders = Order::where('user_id', $userId);
        
        return [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'canceled' => $orders->whereIn('status', ['canceled', 'cancelled', 'error'])->count(),
            'this_month' => $orders->whereMonth('created_at', now()->month)->count(),
            'this_week' => $orders->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }

    private function getTransactionStats($userId)
    {
        $transactions = Transaction::where('user_id', $userId);
        
        return [
            'total_topup' => $transactions->where('type', 'topup')->sum('amount'),
            'total_spent' => abs($transactions->where('type', 'order')->sum('amount')),
            'this_month_topup' => $transactions->where('type', 'topup')
                ->whereMonth('created_at', now()->month)->sum('amount'),
            'this_month_spent' => abs($transactions->where('type', 'order')
                ->whereMonth('created_at', now()->month)->sum('amount')),
        ];
    }

    private function getRecentOrders($userId, $limit = 5)
    {
        return Order::with(['service.category'])
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentTransactions($userId, $limit = 5)
    {
        return Transaction::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getRecentTickets($userId, $limit = 3)
    {
        return Ticket::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function getPopularServices()
    {
        return Service::with('category')
            ->where('active', true)
            ->where('public_active', true)
            ->withCount(['orders' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('orders_count', 'desc')
            ->limit(6)
            ->get();
    }

    private function getChartData($userId)
    {
        $data = [];
        
        // Data untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            
            $ordersCount = Order::where('user_id', $userId)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();
                
            $spentAmount = abs(Transaction::where('user_id', $userId)
                ->where('type', 'order')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('amount'));
            
            $data[] = [
                'date' => $date->format('d M'),
                'orders' => $ordersCount,
                'spent' => $spentAmount,
            ];
        }
        
        return $data;
    }

    private function getNotifications($user)
    {
        $notifications = [];
        
        // Cek saldo rendah
        if (($user->wallet->balance ?? 0) < 10000) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Saldo Rendah',
                'message' => 'Saldo wallet Anda kurang dari Rp 10.000. Silakan top-up untuk melanjutkan order.',
                'icon' => 'wallet',
                'action' => route('wallet.topup'),
                'action_text' => 'Top-up Sekarang'
            ];
        }
        
        // Cek pesanan pending
        $pendingOrders = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        if ($pendingOrders > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Pesanan Pending',
                'message' => "Anda memiliki {$pendingOrders} pesanan yang sedang menunggu diproses.",
                'icon' => 'clock',
                'action' => route('orders.index'),
                'action_text' => 'Lihat Pesanan'
            ];
        }
        
        // Cek ticket belum dibalas
        $unrepliedTickets = Ticket::where('user_id', $user->id)
            ->where('status', 'open')
            ->whereDoesntHave('messages', function($query) {
                $query->where('is_admin', true);
            })
            ->count();
            
        if ($unrepliedTickets > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Ticket Menunggu Balasan',
                'message' => "Anda memiliki {$unrepliedTickets} ticket yang menunggu balasan dari admin.",
                'icon' => 'message',
                'action' => route('tickets.index'),
                'action_text' => 'Lihat Ticket'
            ];
        }
        
        return $notifications;
    }

    private function getTotalSpent($userId)
    {
        return abs(Transaction::where('user_id', $userId)
            ->where('type', 'order')
            ->sum('amount'));
    }

    private function getAverageOrderValue($userId)
    {
        $orders = Order::where('user_id', $userId);
        $totalOrders = $orders->count();
        
        if ($totalOrders === 0) return 0;
        
        return $orders->sum('cost') / $totalOrders;
    }

    private function getSuccessRate($userId)
    {
        $orders = Order::where('user_id', $userId);
        $totalOrders = $orders->count();
        
        if ($totalOrders === 0) return 0;
        
        $completedOrders = $orders->where('status', 'completed')->count();
        
        return round(($completedOrders / $totalOrders) * 100, 1);
    }

    private function getDaysSinceLastOrder($userId)
    {
        $lastOrder = Order::where('user_id', $userId)
            ->latest()
            ->first();
            
        if (!$lastOrder) return null;
        
        return now()->diffInDays($lastOrder->created_at);
    }
}
