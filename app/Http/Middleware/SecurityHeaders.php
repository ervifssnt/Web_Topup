<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable browser XSS protection (legacy but still useful)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Restrict browser features
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');
        
        // HSTS - Force HTTPS for 1 year
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
        
        // Content Security Policy (CSP) - More restrictive
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}