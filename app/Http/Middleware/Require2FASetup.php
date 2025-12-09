<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Require2FASetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to authenticated users
        if ($request->user()) {
            // Check if 2FA is enabled
            if (!$request->user()->google2fa_enabled) {
                // Allow access to 2FA setup routes and logout
                $allowedRoutes = [
                    '2fa.setup.required',
                    '2fa.setup.verify',
                    'logout',
                ];

                if (!$request->routeIs($allowedRoutes)) {
                    return redirect()->route('2fa.setup.required')
                        ->with('warning', 'You must set up Two-Factor Authentication to continue.');
                }
            }
        }

        return $next($request);
    }
}
