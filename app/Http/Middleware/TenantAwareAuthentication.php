<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Tenant;

class TenantAwareAuthentication
{
    /**
     * Handle an incoming request to ensure proper tenant-aware authentication
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Check if we're in a tenant context
        $tenant = $request->attributes->get('tenant');
        
        if ($tenant) {
            // Verify user belongs to this tenant
            if (!$user->belongsToTenant($tenant->id) && !$user->isSuperAdmin()) {
                // Log unauthorized access attempt
                Log::warning('Unauthorized tenant access attempt', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'tenant_id' => $tenant->id,
                    'tenant_domain' => $tenant->domain,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Access denied',
                        'message' => 'You do not have access to this tenant'
                    ], 403);
                }

                // Redirect to user's default tenant or dashboard
                return redirect()->route('dashboard')->withErrors([
                    'access' => 'You do not have access to the requested organization.'
                ]);
            }

            // Set user's role for this tenant
            $tenantRole = $user->getTenantRole($tenant->id);
            $request->attributes->set('user_tenant_role', $tenantRole);
        }

        // Check user status and permissions
        if (!$this->hasValidAccess($user, $request)) {
            return $this->handleInvalidAccess($user, $request);
        }

        return $next($request);
    }

    /**
     * Check if user has valid access
     */
    protected function hasValidAccess(User $user, Request $request): bool
    {
        // Super admin always has access
        if ($user->is_super_admin) {
            return true;
        }

        // Check if user has any meaningful permissions
        $hasPermissions = $user->hasRole(['admin', 'manager', 'accountant']) || 
                         $user->hasAnyPermission(['projects.create', 'expenses.create', 'users.view', 'payments.create', 'reports.generate']);

        // Allow access to welcome routes even without permissions
        $allowedRoutes = [
            'welcome.index',
            'welcome.request-access', 
            'welcome.submit-access-request',
            'profile.edit',
            'profile.update',
            'profile.destroy',
            'role.switch',
            'role.clear',
            'logout'
        ];
        
        if (in_array($request->route()?->getName(), $allowedRoutes)) {
            return true;
        }

        return $hasPermissions;
    }

    /**
     * Handle invalid access
     */
    protected function handleInvalidAccess(User $user, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => 'You do not have the required permissions to access this resource',
                'redirect_url' => route('welcome.index')
            ], 403);
        }

        return redirect()->route('welcome.index')->with('warning', 
            'You need additional permissions to access that section. Please contact your administrator.');
    }
}