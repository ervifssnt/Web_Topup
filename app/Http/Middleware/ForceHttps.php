<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Force HTTPS in production
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce HTTPS in production
        if (!$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}