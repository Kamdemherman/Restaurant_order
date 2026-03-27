<?php
// FILE: app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NewsletterSubscription;
use App\Notifications\AdminNewUserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users'],
            'password'         => ['required', 'confirmed', 'min:8'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'gdpr_consent'     => ['required', 'accepted'],
            'newsletter'       => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'phone'            => $validated['phone'] ?? null,
            'role'             => 'customer',
            'status'           => config('app.require_admin_approval', true) ? 'pending' : 'approved',
            'gdpr_consent'     => true,
            'gdpr_consent_at'  => now(),
            'newsletter_subscribed' => $request->boolean('newsletter'),
        ]);

        // Newsletter subscription
        if ($request->boolean('newsletter')) {
            NewsletterSubscription::firstOrCreate(
                ['email' => $user->email],
                ['name' => $user->name, 'confirmed_at' => now(), 'is_active' => true]
            );
        }

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new AdminNewUserRegistration($user));

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please wait for admin approval before you can log in.');
    }
}
