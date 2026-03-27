<?php
// FILE: app/Http/Controllers/Admin/CouponController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function __construct() { $this->middleware(['auth','admin']); }

    public function index(Request $request)
    {
        $query = Coupon::with('usedBy')->latest();
        if ($request->filled('status')) {
            match($request->status) {
                'active'   => $query->where('is_active', true)->where('is_used', false),
                'used'     => $query->where('is_used', true),
                'inactive' => $query->where('is_active', false),
                default    => null,
            };
        }
        $coupons = $query->paginate(20)->withQueryString();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create() { return view('admin.coupons.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'               => ['required','string','max:50','unique:coupons'],
            'type'               => ['required','in:percentage,fixed'],
            'value'              => ['required','numeric','min:0.01'],
            'min_order_amount'   => ['nullable','numeric','min:0'],
            'max_discount_amount'=> ['nullable','numeric','min:0'],
            'is_single_use'      => ['nullable','boolean'],
            'expires_at'         => ['nullable','date','after:now'],
            'notes'              => ['nullable','string'],
        ]);

        $data['code']          = strtoupper($data['code']);
        $data['is_single_use'] = $request->boolean('is_single_use', true);

        Coupon::create($data);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function generateBulk(Request $request)
    {
        $request->validate(['count' => ['required','integer','min:1','max:100']]);
        $created = 0;
        for ($i = 0; $i < $request->count; $i++) {
            Coupon::create([
                'code'          => strtoupper(Str::random(8)),
                'type'          => $request->type ?? 'fixed',
                'value'         => $request->value ?? 10,
                'is_single_use' => true,
                'expires_at'    => $request->expires_at ?? null,
            ]);
            $created++;
        }
        return back()->with('success', "{$created} coupons generated.");
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        return back()->with('success', 'Coupon status toggled.');
    }
}
