<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\RateLimit;
use App\Models\AuditLog;
use Carbon\Carbon;

class TenantSecurityMiddleware
{
    /**
     * Rate limiting configuration
     */
    protected array $rateLimits = [
        'default' => ['requests' => 1000, 'window' => 3600], // 1000 requests per hour
        'auth' => ['requests' => 10, 'window' => 900],       // 10 login attempts per 15 minutes
        'api' => ['requests' => 100, 'window' => 3600],      // 100 API calls per hour
        'admin' => ['requests' => 200, 'window' => 3600],    // 200 admin actions per hour
    ];

    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next, string $rateType = 'default')
    {
        $tenant = $request->attributes->get('tenant');
        $user = $request->user();

        // Apply rate limiting
        if (!$this->checkRateLimit($request, $tenant, $user, $rateType)) {
            return $this->handleRateLimitExceeded($request, $tenant, $user, $rateType);
        }

        // Check for suspicious activity
        if ($this->detectSuspiciousActivity($request, $tenant, $user)) {
            return $this->handleSuspiciousActivity($request, $tenant, $user);
        }

        // Validate tenant session limits
        if ($user && !$this->validateSessionLimits($tenant, $user)) {
            return $this->handleSessionLimitExceeded($request, $tenant, $user);
        }

        $response = $next($request);

        // Log rate limit usage
        $this->updateRateLimitUsage($request, $tenant, $user, $rateType);

        return $response;
    }

    /**
     * Check if request is within rate limits
     */
    protected function checkRateLimit(Request $request, $tenant, $user, string $rateType): bool
    {
        $limits = $this->rateLimits[$rateType] ?? $this->rateLimits['default'];
        $key = $this->getRateLimitKey($request, $tenant, $user, $rateType);
        
        $currentUsage = Cache::get($key, 0);
        
        return $currentUsage < $limits['requests'];
    }

    /**
     * Generate rate limit cache key
     */
    protected function getRateLimitKey(Request $request, $tenant, $user, string $rateType): string
    {
        $tenantId = $tenant?->id ?? 'guest';
        $userId = $user?->id ?? 'anonymous';
        $ip = $request->ip();
        
        return "rate_limit:{$rateType}:{$tenantId}:{$userId}:{$ip}";
    }

    /**
     * Update rate limit usage
     */
    protected function updateRateLimitUsage(Request $request, $tenant, $user, string $rateType): void
    {
        $limits = $this->rateLimits[$rateType] ?? $this->rateLimits['default'];
        $key = $this->getRateLimitKey($request, $tenant, $user, $rateType);
        
        $currentUsage = Cache::get($key, 0);
        Cache::put($key, $currentUsage + 1, $limits['window']);

        // Store in database for analytics
        RateLimit::create([
            'tenant_id' => $tenant?->id,
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'endpoint' => $request->getPathInfo(),
            'method' => $request->method(),
            'rate_type' => $rateType,
            'usage_count' => $currentUsage + 1,
            'limit_count' => $limits['requests'],
            'window_seconds' => $limits['window'],
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Handle rate limit exceeded
     */
    protected function handleRateLimitExceeded(Request $request, $tenant, $user, string $rateType)
    {
        $limits = $this->rateLimits[$rateType] ?? $this->rateLimits['default'];
        
        // Log rate limit violation
        AuditLog::create([
            'tenant_id' => $tenant?->id,
            'user_id' => $user?->id,
            'action' => 'rate_limit_exceeded',
            'description' => "Rate limit exceeded for {$rateType}",
            'metadata' => [
                'rate_type' => $rateType,
                'limit' => $limits['requests'],
                'window' => $limits['window'],
                'endpoint' => $request->getPathInfo(),
                'method' => $request->method(),
            ],
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => "Too many requests. Limit: {$limits['requests']} per {$limits['window']} seconds",
                'retry_after' => $limits['window']
            ], 429);
        }

        abort(429, 'Too Many Requests');
    }

    /**
     * Detect suspicious activity patterns
     */
    protected function detectSuspiciousActivity(Request $request, $tenant, $user): bool
    {
        $ip = $request->ip();
        $timeWindow = 300; // 5 minutes

        // Check for multiple failed login attempts
        if ($this->isLoginEndpoint($request)) {
            $failedAttempts = AuditLog::where('ip_address', $ip)
                ->where('action', 'login_failed')
                ->where('created_at', '>=', Carbon::now()->subSeconds($timeWindow))
                ->count();

            if ($failedAttempts >= 5) {
                return true;
            }
        }

        // Check for rapid-fire requests from same IP
        $recentRequests = Cache::get("requests:{$ip}", []);
        $now = time();
        
        // Clean old requests
        $recentRequests = array_filter($recentRequests, function($timestamp) use ($now) {
            return ($now - $timestamp) < 60; // Last minute
        });
        
        if (count($recentRequests) > 100) { // More than 100 requests per minute
            return true;
        }

        // Update request tracking
        $recentRequests[] = $now;
        Cache::put("requests:{$ip}", $recentRequests, 300);

        return false;
    }

    /**
     * Handle suspicious activity
     */
    protected function handleSuspiciousActivity(Request $request, $tenant, $user)
    {
        // Log security incident
        AuditLog::create([
            'tenant_id' => $tenant?->id,
            'user_id' => $user?->id,
            'action' => 'suspicious_activity',
            'description' => 'Suspicious activity detected',
            'metadata' => [
                'endpoint' => $request->getPathInfo(),
                'method' => $request->method(),
                'detection_reason' => 'Multiple violations detected',
            ],
            'severity' => 'high',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Temporarily block IP
        $blockKey = "blocked_ip:{$request->ip()}";
        Cache::put($blockKey, true, 3600); // Block for 1 hour

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Access temporarily restricted',
                'message' => 'Suspicious activity detected. Access temporarily restricted.'
            ], 403);
        }

        abort(403, 'Access temporarily restricted');
    }

    /**
     * Validate session limits for tenant
     */
    protected function validateSessionLimits($tenant, $user): bool
    {
        if (!$tenant || !$user) {
            return true;
        }

        // Check if tenant has session limits configured
        $maxSessions = $tenant->max_concurrent_sessions ?? 0;
        
        if ($maxSessions > 0) {
            $activeSessions = DB::table('user_sessions')
                ->where('tenant_id', $tenant->id)
                ->where('user_id', $user->id)
                ->where('last_activity', '>=', Carbon::now()->subMinutes(30))
                ->count();

            return $activeSessions <= $maxSessions;
        }

        return true;
    }

    /**
     * Handle session limit exceeded
     */
    protected function handleSessionLimitExceeded(Request $request, $tenant, $user)
    {
        // Log session limit violation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'session_limit_exceeded',
            'description' => 'Concurrent session limit exceeded',
            'metadata' => [
                'max_sessions' => $tenant->max_concurrent_sessions,
            ],
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Session limit exceeded',
                'message' => 'Maximum number of concurrent sessions reached'
            ], 409);
        }

        return redirect()->route('login')->withErrors([
            'session' => 'Maximum number of concurrent sessions reached. Please try again later.'
        ]);
    }

    /**
     * Check if request is to login endpoint
     */
    protected function isLoginEndpoint(Request $request): bool
    {
        $loginRoutes = ['login', 'auth.login', 'api.login'];
        return in_array($request->route()?->getName(), $loginRoutes);
    }
}