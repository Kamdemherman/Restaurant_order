<?php
// FILE: app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use App\Models\GdprRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'approved']);
    }

    public function edit()
    {
        return view('user.profile.edit', [
            'user'            => auth()->user(),
            'googleMapsKey'   => config('services.google_maps.key'),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'lat'     => ['nullable', 'numeric'],
            'lng'     => ['nullable', 'numeric'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function updateNewsletter(Request $request)
    {
        $user = auth()->user();
        $subscribe = $request->boolean('newsletter_subscribed');

        $user->update(['newsletter_subscribed' => $subscribe]);

        if ($subscribe) {
            NewsletterSubscription::updateOrCreate(
                ['email' => $user->email],
                ['name' => $user->name, 'is_active' => true, 'confirmed_at' => now()]
            );
        } else {
            NewsletterSubscription::where('email', $user->email)->update([
                'is_active'        => false,
                'unsubscribed_at'  => now(),
            ]);
        }

        return back()->with('success', 'Newsletter preference updated.');
    }

    public function gdprExport(Request $request)
    {
        $user = auth()->user();

        // Create GDPR request record
        \App\Models\GdprRequest::create(['user_id' => $user->id, 'type' => 'export']);

        $data = [
            'profile'  => $user->only('name', 'email', 'phone', 'address', 'created_at'),
            'orders'   => $user->orders()->with('items')->get()->toArray(),
            'exported' => now()->toDateTimeString(),
        ];

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="my-data-' . now()->format('Y-m-d') . '.json"');
    }

    public function gdprDelete(Request $request)
    {
        $request->validate(['confirmation' => ['required', 'in:DELETE']]);

        $user = auth()->user();
        \App\Models\GdprRequest::create(['user_id' => $user->id, 'type' => 'delete']);

        // Anonymise rather than hard delete (retains order records for accounting)
        $user->update([
            'name'                 => 'Deleted User',
            'email'                => 'deleted+' . $user->id . '@example.com',
            'phone'                => null,
            'address'              => null,
            'lat'                  => null,
            'lng'                  => null,
            'newsletter_subscribed' => false,
            'status'               => 'suspended',
        ]);

        NewsletterSubscription::where('email', $user->email)->update(['is_active' => false]);

        auth()->logout();
        $request->session()->invalidate();

        return redirect()->route('login')->with('success', 'Your account data has been removed.');
    }
}
