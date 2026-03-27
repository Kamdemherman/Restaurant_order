<?php
// FILE: app/Http/Controllers/OrderController.php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Coupon;
use App\Services\PrinterService;
use App\Notifications\OrderConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private PrinterService $printer)
    {
        $this->middleware(['auth', 'approved']);
    }

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.menuItem', 'coupon');
        return view('user.orders.show', compact('order'));
    }

    public function checkout()
    {
        return view('user.orders.checkout', [
            'user' => auth()->user(),
            'googleMapsKey' => config('services.google_maps.key'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.addons'   => ['nullable', 'array'],
            'items.*.notes'    => ['nullable', 'string', 'max:200'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'delivery_lat'     => ['nullable', 'numeric'],
            'delivery_lng'     => ['nullable', 'numeric'],
            'coupon_code'      => ['nullable', 'string'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::with('availableAddons')->findOrFail($itemData['id']);

                if (!$menuItem->is_available) {
                    return back()->withErrors(['items' => "'{$menuItem->name}' is currently unavailable."]);
                }

                $itemPrice = $menuItem->price;
                $selectedAddons = [];

                if (!empty($itemData['addons'])) {
                    foreach ($itemData['addons'] as $addonId) {
                        $addon = $menuItem->availableAddons->find($addonId);
                        if ($addon) {
                            $itemPrice += $addon->price;
                            $selectedAddons[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price];
                        }
                    }
                }

                $itemSubtotal = $itemPrice * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'menu_item_id'    => $menuItem->id,
                    'name'            => $menuItem->name,
                    'price'           => $itemPrice,
                    'quantity'        => $itemData['quantity'],
                    'subtotal'        => $itemSubtotal,
                    'selected_addons' => $selectedAddons ?: null,
                    'notes'           => $itemData['notes'] ?? null,
                ];
            }

            // Apply coupon
            $discountAmount = 0;
            $coupon = null;
            if (!empty($validated['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();
                if (!$coupon || !$coupon->isValid($subtotal)) {
                    return back()->withErrors(['coupon_code' => 'Invalid or expired coupon code.']);
                }
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }

            $total = max(0, $subtotal - $discountAmount);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'coupon_id'        => $coupon?->id,
                'status'           => 'pending',
                'subtotal'         => $subtotal,
                'discount_amount'  => $discountAmount,
                'total'            => $total,
                'delivery_address' => $validated['delivery_address'],
                'delivery_lat'     => $validated['delivery_lat'] ?? null,
                'delivery_lng'     => $validated['delivery_lng'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'payment_method'   => 'cash_on_delivery',
                'payment_status'   => 'pending',
            ]);

            $order->items()->createMany($orderItemsData);

            // Mark coupon as used
            if ($coupon && $coupon->is_single_use) {
                $coupon->update([
                    'is_used' => true,
                    'used_by' => auth()->id(),
                    'used_at' => now(),
                ]);
            }

            // Notify user
            auth()->user()->notify(new OrderConfirmed($order));

            // Auto-print if configured
            $this->printer->printOrder($order);

            return redirect()->route('orders.show', $order)
                ->with('success', "Order #{$order->order_number} placed successfully!");
        });
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code'   => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValid($request->amount)) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon.']);
        }

        return response()->json([
            'valid'    => true,
            'discount' => $coupon->calculateDiscount($request->amount),
            'message'  => 'Coupon applied!',
        ]);
    }
}
