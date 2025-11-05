<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check if user is not authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // Skip check for routes that should be accessible to all authenticated users
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
        
        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }
        
        // Check if user has any meaningful permissions or elevated roles
        if (!$user->hasRole(['admin', 'manager', 'accountant']) && 
            !$user->hasAnyPermission(['projects.create', 'expenses.create', 'users.view', 'payments.create', 'reports.generate'])) {
            // Redirect users with no permissions to welcome page
            return redirect()->route('welcome.index');
        }

        return $next($request);
    }
}