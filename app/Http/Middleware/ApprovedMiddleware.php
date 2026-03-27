<?php
// FILE: app/Http/Middleware/ApprovedMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->status === 'pending') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is awaiting admin approval.']);
        }

        if ($user->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        }

        return $next($request);
    }
}
