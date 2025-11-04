<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Add baseline security headers to every response.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Clickjacking protection
        $response->headers->set('X-Frame-Options', 'DENY');

        // MIME sniffing protection
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Basic referrer policy
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');

        // Reduce available powerful features by default
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Only set HSTS on HTTPS requests to avoid issues in local dev
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        // Note: We intentionally do not set a Content-Security-Policy here to avoid breaking assets.
        // If you want CSP, we can add a tailored policy later.

        return $response;
    }
}
