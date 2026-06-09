<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MfaVerificationCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && session('mfa_pending') === true) {
            $excludedRoutes = [
                'mfa.verify',
                'mfa.verify.post',
                'logout'
            ];

            if (!in_array($request->route()?->getName(), $excludedRoutes)) {
                return redirect()->route('mfa.verify')
                    ->with('warning', 'Silakan masukkan kode OTP yang dikirimkan ke email Anda untuk melanjutkan.');
            }
        }

        return $next($request);
    }
}
