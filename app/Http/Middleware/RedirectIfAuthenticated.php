<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request and redirect authenticated users appropriately
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $this->redirectAuthenticatedUser($request, Auth::guard($guard)->user());
            }
        }

        return $next($request);
    }

    /**
     * Redirect authenticated user based on their role and permissions
     */
    protected function redirectAuthenticatedUser(Request $request, User $user)
    {
        // Check if user has meaningful permissions
        $hasPermissions = $user->hasRole(['admin', 'manager', 'accountant']) || 
                         $user->hasAnyPermission(['projects.create', 'expenses.create', 'users.view', 'payments.create', 'reports.generate']);

        // If user has no permissions, redirect to welcome page
        if (!$hasPermissions) {
            return redirect()->route('welcome.index');
        }

        // For API requests, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Already authenticated',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_super_admin' => $user->is_super_admin
                ],
                'redirect_url' => $this->getDashboardRoute($user)
            ], 200);
        }

        // Redirect to appropriate dashboard based on role
        return redirect($this->getDashboardRoute($user));
    }

    /**
     * Get the appropriate dashboard route for the user
     */
    protected function getDashboardRoute(User $user): string
    {
        // Super admin gets special treatment
        if ($user->is_super_admin) {
            return route('dashboard');
        }

        // Role-based dashboard routing
        if ($user->hasRole('admin')) {
            return route('dashboard');
        } elseif ($user->hasRole('accountant')) {
            return route('dashboard');
        } elseif ($user->hasRole('manager')) {
            return route('dashboard');
        }

        // Default to dashboard for users with any permissions
        return route('dashboard');
    }
}