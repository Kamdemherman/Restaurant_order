<?php
// FILE: app/Http/Controllers/Admin/ReportController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct() { $this->middleware(['auth','admin']); }

    public function sales(Request $request)
    {
        $from = $request->date('from', 'Y-m-d') ?? now()->startOfMonth();
        $to   = $request->date('to', 'Y-m-d')   ?? now()->endOfDay();

        $summary = Order::whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as total_orders, SUM(total) as total_revenue, SUM(discount_amount) as total_discounts, AVG(total) as avg_order')
            ->first();

        $dailySales = Order::whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topItems = OrderItem::whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])->where('status', '!=', 'cancelled');
            })
            ->select('name', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(subtotal) as revenue'))
            ->groupBy('name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        $byStatus = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.reports.sales', compact('summary','dailySales','topItems','byStatus','from','to'));
    }

    public function exportCsv(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $orders = Order::with(['user','items'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $csv  = "Order Number,Customer,Status,Subtotal,Discount,Total,Date\n";
        foreach ($orders as $order) {
            $csv .= "\"{$order->order_number}\",\"{$order->user->name}\",\"{$order->status}\","
                . "\"{$order->subtotal}\",\"{$order->discount_amount}\",\"{$order->total}\","
                . "\"{$order->created_at->format('Y-m-d H:i')}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"sales-{$from}-{$to}.csv\"");
    }
}
