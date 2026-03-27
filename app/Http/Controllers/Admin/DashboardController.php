<?php
// FILE: app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $stats = [
            'orders_today'       => Order::whereDate('created_at', today())->count(),
            'revenue_today'      => Order::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total'),
            'orders_this_month'  => Order::whereMonth('created_at', now()->month)->count(),
            'revenue_this_month' => Order::whereMonth('created_at', now()->month)->where('status', '!=', 'cancelled')->sum('total'),
            'pending_users'      => User::where('status', 'pending')->count(),
            'total_customers'    => User::where('role', 'customer')->where('status', 'approved')->count(),
            'pending_orders'     => Order::where('status', 'pending')->count(),
            'active_items'       => MenuItem::where('is_available', true)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $salesChart = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'salesChart'));
    }
}
