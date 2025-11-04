<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Add common security headers to all HTTP responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Core security headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Permissions Policy (restrict powerful APIs by default)
        $response->headers->set('Permissions-Policy',
            "camera=(), microphone=(), geolocation=(), fullscreen=(self), payment=()"
        );

        // Strict-Transport-Security (only when using HTTPS in non-local env)
        if ($request->isSecure() && app()->environment('production')) {
            // 6 months, include subdomains, preload eligible
            $response->headers->set('Strict-Transport-Security', 'max-age=15552000; includeSubDomains; preload');
        }

        // Content Security Policy - Report-Only to avoid breaking existing inline/CDN usage
        // Adjust this to an enforcing header once all assets adhere to the policy.
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];
        $response->headers->set('Content-Security-Policy-Report-Only', implode('; ', $csp));

        return $response;
    }
}
