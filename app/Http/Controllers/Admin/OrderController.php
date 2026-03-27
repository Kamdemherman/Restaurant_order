<?php
// FILE: app/Http/Controllers/Admin/OrderController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PrinterService;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private PrinterService $printer)
    {
        $this->middleware(['auth','admin']);
    }

    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }

        $orders = $query->paginate(25)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user','items.menuItem','coupon');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => ['required','in:pending,confirmed,preparing,ready,delivered,cancelled']]);

        $order->update(['status' => $request->status]);
        $order->user->notify(new OrderStatusUpdated($order));

        return back()->with('success', "Order status updated to {$request->status}.");
    }

    public function print(Order $order)
    {
        $result = $this->printer->printOrder($order, force: true);

        if ($result) {
            return back()->with('success', 'Order sent to printer.');
        }
        return back()->withErrors(['print' => 'Printing failed. Check printer configuration.']);
    }

    public function bulkPrint(Request $request)
    {
        $request->validate(['order_ids' => ['required','array']]);
        $orders = Order::whereIn('id', $request->order_ids)->with('items')->get();
        $count  = 0;
        foreach ($orders as $order) {
            if ($this->printer->printOrder($order, force: true)) $count++;
        }
        return back()->with('success', "{$count} orders sent to printer.");
    }
}
