<?php
// FILE: app/Http/Controllers/Admin/NewsletterController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct() { $this->middleware(['auth','admin']); }

    public function index(Request $request)
    {
        $query = NewsletterSubscription::query();
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        $subscriptions = $query->latest()->paginate(30)->withQueryString();
        return view('admin.newsletter.index', compact('subscriptions'));
    }

    public function export()
    {
        $subscriptions = NewsletterSubscription::where('is_active', true)
            ->orderBy('email')
            ->get(['email', 'name', 'confirmed_at']);

        $csv = "Email,Name,Subscribed At\n";
        foreach ($subscriptions as $sub) {
            $csv .= "\"{$sub->email}\",\"{$sub->name}\",\"{$sub->confirmed_at}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="newsletter-' . now()->format('Y-m-d') . '.csv"');
    }

    public function destroy(NewsletterSubscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'Subscription removed.');
    }
}
