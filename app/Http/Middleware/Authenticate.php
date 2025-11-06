<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, don't redirect - return 401 instead
        if ($request->expectsJson()) {
            return null;
        }

        // For multi-tenant API requests
        if ($request->is('api/mt/*') || $request->is('api/v1/*')) {
            return null;
        }

        // For web requests, redirect to login
        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        // For API requests, return JSON error
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Authentication required to access this resource'
            ], 401);
        }

        // For web requests, redirect to login
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', 
            $guards, 
            $this->redirectTo($request)
        );
    }
}