<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantDataMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // For super admins, allow access to all tenant data
        if ($user->is_super_admin) {
            // Super admin can optionally specify a tenant context via query parameter
            $tenantId = $request->query('tenant_id');
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
                if ($tenant) {
                    $this->setTenantContext($tenant);
                }
            }
            return $next($request);
        }

        // For regular users, ensure they have a tenant context
        $currentTenant = $this->getCurrentTenantForUser($user);
        
        if (!$currentTenant) {
            // User has no tenant access - redirect to welcome or access request page
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No tenant access',
                    'message' => 'You do not have access to any tenant data.'
                ], 403);
            }
            
            return redirect('/')
                ->with('error', 'You need to be assigned to a tenant to access this data.');
        }

        // Set the tenant context
        $this->setTenantContext($currentTenant);

        // Add tenant information to the request
        $request->merge(['current_tenant' => $currentTenant]);

        return $next($request);
    }

    /**
     * Get the current tenant for the authenticated user.
     */
    protected function getCurrentTenantForUser($user): ?Tenant
    {
        // Check if user has a current_tenant_id set
        if ($user->current_tenant_id) {
            $tenant = Tenant::find($user->current_tenant_id);
            if ($tenant && $user->belongsToTenant($tenant->id)) {
                return $tenant;
            }
        }

        // If no current tenant set or invalid, get the first available tenant
        return $user->tenants()->first();
    }

    /**
     * Set the tenant context in the application container.
     */
    protected function setTenantContext(Tenant $tenant): void
    {
        app()->singleton('currentTenant', function () use ($tenant) {
            return $tenant;
        });

        // Also set it in the session for consistency
        session(['current_tenant_id' => $tenant->id]);
    }
}
