<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for API routes or if no authentication required
        if ($request->is('api/*') || !Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentTenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        // Super admins can access all tenants
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // If there's a current tenant, check if user belongs to it
        if ($currentTenant) {
            if (!$user->belongsToTenant($currentTenant->id)) {
                abort(403, 'You do not have access to this tenant.');
            }
        } else {
            // No tenant context - redirect to tenant selection or first tenant
            $userTenants = $user->tenants;
            
            if ($userTenants->isEmpty()) {
                abort(403, 'You do not belong to any tenant.');
            }

            // Redirect to first tenant's domain
            $firstTenant = $userTenants->first();
            $tenantUrl = $this->buildTenantUrl($request, $firstTenant->domain);
            
            return redirect($tenantUrl);
        }

        return $next($request);
    }

    /**
     * Build URL for a tenant based on current request and tenant domain.
     */
    protected function buildTenantUrl(Request $request, string $tenantDomain): string
    {
        $scheme = $request->getScheme();
        $host = $request->getHost();
        $port = $request->getPort();
        $path = $request->getPathInfo();
        $query = $request->getQueryString();

        // For development, you might want to handle this differently
        // Example: redirect to tenant1.yourdomain.test
        if (in_array($host, ['localhost', '127.0.0.1'])) {
            // For local development, you might want to use a different pattern
            // or redirect to a tenant selection page
            return route('tenant.select');
        }

        // Build tenant URL with subdomain
        $parts = explode('.', $host);
        if (count($parts) >= 2) {
            $parts[0] = $tenantDomain;
            $tenantHost = implode('.', $parts);
        } else {
            $tenantHost = $tenantDomain . '.' . $host;
        }

        $url = $scheme . '://' . $tenantHost;
        
        if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
            $url .= ':' . $port;
        }
        
        $url .= $path;
        
        if ($query) {
            $url .= '?' . $query;
        }

        return $url;
    }
}
