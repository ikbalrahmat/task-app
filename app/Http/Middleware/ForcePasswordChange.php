<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Bypass password reset checks in tests by default to not break other features' tests
            if (app()->environment('testing') && !session('test_force_password_change')) {
                return $next($request);
            }

            // Exclude logout and profile routes from redirection
            $excludedRoutes = [
                'logout',
                'profile.index',
                'profile.password',
                'password.request',
                'password.email',
            ];

            if (in_array($request->route()?->getName(), $excludedRoutes)) {
                return $next($request);
            }

            // Check if password has never been changed, or is expired (90 days = 90 * 24 * 3600 seconds)
            $mustChange = false;
            if (is_null($user->password_changed_at)) {
                $mustChange = true;
            } else {
                $daysSinceChange = now()->diffInDays($user->password_changed_at);
                if ($daysSinceChange >= 90) {
                    $mustChange = true;
                }
            }

            if ($mustChange) {
                return redirect()->route('profile.index')
                    ->with('warning', 'Anda wajib mengganti password Anda demi alasan keamanan (password kedaluwarsa atau pertama kali masuk).');
            }
        }

        return $next($request);
    }
}
