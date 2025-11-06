<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\AuditLog;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ResolveTenantMiddleware
{
    /**
     * Handle an incoming request to resolve tenant context
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->resolveTenant($request);
        
        if (!$tenant) {
            return $this->handleMissingTenant($request);
        }

        // Security checks
        if (!$this->validateTenantAccess($tenant, $request)) {
            return $this->handleUnauthorizedAccess($tenant, $request);
        }

        // Set tenant context
        $this->setTenantContext($tenant, $request);
        
        // Log access if needed
        $this->logTenantAccess($tenant, $request);

        return $next($request);
    }

    /**
     * Resolve tenant from request using multiple strategies
     */
    protected function resolveTenant(Request $request): ?Tenant
    {
        // Strategy 1: Subdomain resolution (primary)
        if ($tenant = $this->resolveFromSubdomain($request)) {
            return $tenant;
        }

        // Strategy 2: Custom header (for API)
        if ($tenant = $this->resolveFromHeader($request)) {
            return $tenant;
        }

        // Strategy 3: JWT token tenant claim
        if ($tenant = $this->resolveFromToken($request)) {
            return $tenant;
        }

        // Strategy 4: Query parameter (development/testing)
        if ($tenant = $this->resolveFromQuery($request)) {
            return $tenant;
        }

        return null;
    }

    /**
     * Resolve tenant from subdomain
     */
    protected function resolveFromSubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $baseDomain = config('app.domain', 'siteledger.com');
        
        // Extract subdomain
        if (str_ends_with($host, ".{$baseDomain}")) {
            $subdomain = str_replace(".{$baseDomain}", '', $host);
            
            if ($subdomain && $subdomain !== 'www' && $subdomain !== 'admin') {
                return Tenant::where('domain', $subdomain)
                           ->where('status', 'active')
                           ->first();
            }
        }

        return null;
    }

    /**
     * Resolve tenant from custom header
     */
    protected function resolveFromHeader(Request $request): ?Tenant
    {
        $tenantId = $request->header('X-Tenant-ID');
        $tenantDomain = $request->header('X-Tenant-Domain');

        if ($tenantId) {
            return Tenant::where('id', $tenantId)
                        ->where('status', 'active')
                        ->first();
        }

        if ($tenantDomain) {
            return Tenant::where('domain', $tenantDomain)
                        ->where('status', 'active')
                        ->first();
        }

        return null;
    }

    /**
     * Resolve tenant from JWT token
     */
    protected function resolveFromToken(Request $request): ?Tenant
    {
        $user = $request->user('api');
        
        if (!$user) {
            return null;
        }

        // Check if user has a current tenant context
        if ($user->current_tenant_id) {
            return Tenant::where('id', $user->current_tenant_id)
                        ->where('status', 'active')
                        ->whereHas('users', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->first();
        }

        return null;
    }

    /**
     * Resolve tenant from query parameter (development only)
     */
    protected function resolveFromQuery(Request $request): ?Tenant
    {
        if (!app()->environment(['local', 'testing'])) {
            return null;
        }

        if ($tenantId = $request->query('tenant_id')) {
            return Tenant::where('id', $tenantId)
                        ->where('status', 'active')
                        ->first();
        }

        return null;
    }

    /**
     * Validate user has access to resolved tenant
     */
    protected function validateTenantAccess(Tenant $tenant, Request $request): bool
    {
        // Allow access for guest requests to public endpoints
        $user = $request->user();
        
        if (!$user) {
            return $this->isPublicEndpoint($request);
        }

        // Super admins can access any tenant
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check if user belongs to this tenant
        if (!$user->belongsToTenant($tenant->id)) {
            return false;
        }

        // Check tenant status
        if (!$tenant->isActive()) {
            return false;
        }

        // Check subscription status
        if (!$tenant->hasActiveSubscription()) {
            return $this->isSubscriptionEndpoint($request);
        }

        return true;
    }

    /**
     * Check if endpoint allows public access
     */
    protected function isPublicEndpoint(Request $request): bool
    {
        $publicRoutes = [
            'login',
            'register', 
            'password.request',
            'password.email',
            'password.reset',
            'verification.notice',
            'verification.verify',
            'invitation.accept'
        ];

        return in_array($request->route()?->getName(), $publicRoutes);
    }

    /**
     * Check if endpoint is related to subscription management
     */
    protected function isSubscriptionEndpoint(Request $request): bool
    {
        $subscriptionRoutes = [
            'billing.index',
            'billing.update',
            'subscription.cancel'
        ];

        return in_array($request->route()?->getName(), $subscriptionRoutes);
    }

    /**
     * Set tenant context in application
     */
    protected function setTenantContext(Tenant $tenant, Request $request): void
    {
        // Set current tenant in service container
        app()->instance('currentTenant', $tenant);
        
        // Make tenant current for Spatie Multitenancy
        $tenant->makeCurrent();
        
        // Store in request for easy access
        $request->attributes->set('tenant', $tenant);
        
        // Set tenant context for session
        if ($request->hasSession()) {
            $request->session()->put('current_tenant_id', $tenant->id);
        }
    }

    /**
     * Log tenant access for security monitoring
     */
    protected function logTenantAccess(Tenant $tenant, Request $request): void
    {
        // Only log significant events, not every request
        if ($this->shouldLogAccess($request)) {
            AuditLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $request->user()?->id,
                'action' => 'tenant_access',
                'description' => 'User accessed tenant: ' . $tenant->name,
                'metadata' => [
                    'method' => $request->method(),
                    'uri' => $request->getRequestUri(),
                    'user_agent' => $request->userAgent(),
                ],
                'severity' => 'low',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
    }

    /**
     * Determine if access should be logged
     */
    protected function shouldLogAccess(Request $request): bool
    {
        // Log first access in session
        if ($request->hasSession() && !$request->session()->has('tenant_access_logged')) {
            $request->session()->put('tenant_access_logged', true);
            return true;
        }

        // Log admin or sensitive endpoints
        $sensitiveRoutes = [
            'admin.',
            'users.',
            'settings.',
            'billing.',
            'api.',
        ];

        $routeName = $request->route()?->getName() ?? '';
        
        foreach ($sensitiveRoutes as $pattern) {
            if (str_starts_with($routeName, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle missing tenant
     */
    protected function handleMissingTenant(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant not found',
                'message' => 'Invalid or missing tenant identifier'
            ], 404);
        }

        // Redirect to tenant selection or main domain
        return redirect()->to(config('app.url'));
    }

    /**
     * Handle unauthorized tenant access
     */
    protected function handleUnauthorizedAccess(Tenant $tenant, Request $request)
    {
        // Log security violation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $request->user()?->id,
            'action' => 'access_denied',
            'description' => 'Unauthorized tenant access attempt',
            'metadata' => [
                'tenant_domain' => $tenant->domain,
                'method' => $request->method(),
                'uri' => $request->getRequestUri(),
            ],
            'severity' => 'high',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'You do not have access to this tenant'
            ], 403);
        }

        abort(403, 'Access denied to this tenant');
    }
}