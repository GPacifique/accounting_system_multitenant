<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Display API management dashboard.
     */
    public function index()
    {
        $apiKeys = $this->getApiKeys();
        $stats = [
            'total_keys' => count($apiKeys),
            'active_keys' => count(array_filter($apiKeys, fn($key) => $key['status'] === 'active')),
            'requests_today' => $this->getRequestsToday(),
            'rate_limit_hits' => $this->getRateLimitHits(),
        ];

        $recentRequests = $this->getRecentApiRequests();
        $endpoints = $this->getApiEndpoints();

        return view('admin.api.index', compact('apiKeys', 'stats', 'recentRequests', 'endpoints'));
    }

    /**
     * Create new API key.
     */
    public function createKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'rate_limit' => 'required|integer|min:1|max:10000',
            'permissions' => 'array',
        ]);

        $apiKey = [
            'id' => Str::uuid(),
            'name' => $request->name,
            'description' => $request->description,
            'key' => 'ak_' . Str::random(32),
            'secret' => Str::random(64),
            'rate_limit' => $request->rate_limit,
            'permissions' => $request->permissions ?? [],
            'status' => 'active',
            'created_at' => now(),
            'last_used_at' => null,
            'usage_count' => 0,
        ];

        $this->storeApiKey($apiKey);

        return back()->with('success', 'API key created successfully.')
                    ->with('new_api_key', $apiKey);
    }

    /**
     * Update API key.
     */
    public function updateKey(Request $request, $keyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'rate_limit' => 'required|integer|min:1|max:10000',
            'status' => 'required|in:active,suspended,revoked',
            'permissions' => 'array',
        ]);

        $apiKeys = $this->getApiKeys();
        $keyIndex = array_search($keyId, array_column($apiKeys, 'id'));

        if ($keyIndex === false) {
            return back()->with('error', 'API key not found.');
        }

        $apiKeys[$keyIndex] = array_merge($apiKeys[$keyIndex], [
            'name' => $request->name,
            'description' => $request->description,
            'rate_limit' => $request->rate_limit,
            'status' => $request->status,
            'permissions' => $request->permissions ?? [],
            'updated_at' => now(),
        ]);

        $this->storeApiKeys($apiKeys);

        return back()->with('success', 'API key updated successfully.');
    }

    /**
     * Revoke API key.
     */
    public function revokeKey($keyId)
    {
        $apiKeys = $this->getApiKeys();
        $keyIndex = array_search($keyId, array_column($apiKeys, 'id'));

        if ($keyIndex === false) {
            return back()->with('error', 'API key not found.');
        }

        $apiKeys[$keyIndex]['status'] = 'revoked';
        $apiKeys[$keyIndex]['revoked_at'] = now();

        $this->storeApiKeys($apiKeys);

        return back()->with('success', 'API key revoked successfully.');
    }

    /**
     * Regenerate API key secret.
     */
    public function regenerateSecret($keyId)
    {
        $apiKeys = $this->getApiKeys();
        $keyIndex = array_search($keyId, array_column($apiKeys, 'id'));

        if ($keyIndex === false) {
            return back()->with('error', 'API key not found.');
        }

        $newSecret = Str::random(64);
        $apiKeys[$keyIndex]['secret'] = $newSecret;
        $apiKeys[$keyIndex]['updated_at'] = now();

        $this->storeApiKeys($apiKeys);

        return back()->with('success', 'API key secret regenerated successfully.')
                    ->with('new_secret', $newSecret);
    }

    /**
     * Show API documentation.
     */
    public function documentation()
    {
        $endpoints = $this->getApiEndpoints();
        return view('admin.api.documentation', compact('endpoints'));
    }

    /**
     * Show rate limiting configuration.
     */
    public function rateLimits()
    {
        $globalLimits = $this->getGlobalRateLimits();
        $rateLimitStats = $this->getRateLimitStats();

        return view('admin.api.rate-limits', compact('globalLimits', 'rateLimitStats'));
    }

    /**
     * Update global rate limits.
     */
    public function updateGlobalRateLimits(Request $request)
    {
        $request->validate([
            'default_limit' => 'required|integer|min:1|max:100000',
            'burst_limit' => 'required|integer|min:1|max:100000',
            'window_minutes' => 'required|integer|min:1|max:1440',
        ]);

        $limits = [
            'default_limit' => $request->default_limit,
            'burst_limit' => $request->burst_limit,
            'window_minutes' => $request->window_minutes,
            'updated_at' => now(),
        ];

        Cache::put('api_global_rate_limits', $limits, now()->addDays(30));

        return back()->with('success', 'Global rate limits updated successfully.');
    }

    /**
     * Show API analytics.
     */
    public function analytics()
    {
        $analytics = [
            'requests_by_hour' => $this->getRequestsByHour(),
            'top_endpoints' => $this->getTopEndpoints(),
            'error_rates' => $this->getErrorRates(),
            'response_times' => $this->getResponseTimes(),
        ];

        return view('admin.api.analytics', compact('analytics'));
    }

    /**
     * Get stored API keys.
     */
    protected function getApiKeys()
    {
        return Cache::get('api_keys', []);
    }

    /**
     * Store single API key.
     */
    protected function storeApiKey($apiKey)
    {
        $apiKeys = $this->getApiKeys();
        $apiKeys[] = $apiKey;
        $this->storeApiKeys($apiKeys);
    }

    /**
     * Store API keys array.
     */
    protected function storeApiKeys($apiKeys)
    {
        Cache::put('api_keys', $apiKeys, now()->addDays(30));
    }

    /**
     * Get API endpoints configuration.
     */
    protected function getApiEndpoints()
    {
        return [
            'tenants' => [
                'GET /api/tenants' => 'List all tenants',
                'POST /api/tenants' => 'Create new tenant',
                'GET /api/tenants/{id}' => 'Get tenant details',
                'PUT /api/tenants/{id}' => 'Update tenant',
                'DELETE /api/tenants/{id}' => 'Delete tenant',
            ],
            'users' => [
                'GET /api/users' => 'List all users',
                'POST /api/users' => 'Create new user',
                'GET /api/users/{id}' => 'Get user details',
                'PUT /api/users/{id}' => 'Update user',
                'DELETE /api/users/{id}' => 'Delete user',
            ],
            'subscriptions' => [
                'GET /api/subscriptions' => 'List all subscriptions',
                'POST /api/subscriptions' => 'Create new subscription',
                'GET /api/subscriptions/{id}' => 'Get subscription details',
                'PUT /api/subscriptions/{id}' => 'Update subscription',
            ],
            'analytics' => [
                'GET /api/analytics/dashboard' => 'Get dashboard analytics',
                'GET /api/analytics/reports' => 'Get detailed reports',
            ],
        ];
    }

    /**
     * Get requests made today.
     */
    protected function getRequestsToday()
    {
        // This would integrate with actual API request logging
        return Cache::get('api_requests_today', 0);
    }

    /**
     * Get rate limit hits.
     */
    protected function getRateLimitHits()
    {
        return Cache::get('api_rate_limit_hits_today', 0);
    }

    /**
     * Get recent API requests.
     */
    protected function getRecentApiRequests()
    {
        // This would come from actual API request logs
        return Cache::get('recent_api_requests', []);
    }

    /**
     * Get global rate limits.
     */
    protected function getGlobalRateLimits()
    {
        return Cache::get('api_global_rate_limits', [
            'default_limit' => 1000,
            'burst_limit' => 2000,
            'window_minutes' => 60,
        ]);
    }

    /**
     * Get rate limit statistics.
     */
    protected function getRateLimitStats()
    {
        return [
            'total_requests' => 0,
            'blocked_requests' => 0,
            'top_consumers' => [],
        ];
    }

    /**
     * Get requests by hour for the last 24 hours.
     */
    protected function getRequestsByHour()
    {
        $data = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $data[] = [
                'hour' => $hour->format('H:00'),
                'requests' => rand(10, 100), // Placeholder data
            ];
        }
        return $data;
    }

    /**
     * Get top API endpoints by usage.
     */
    protected function getTopEndpoints()
    {
        return [
            ['endpoint' => '/api/tenants', 'requests' => 245],
            ['endpoint' => '/api/users', 'requests' => 198],
            ['endpoint' => '/api/subscriptions', 'requests' => 156],
            ['endpoint' => '/api/analytics/dashboard', 'requests' => 89],
        ];
    }

    /**
     * Get API error rates.
     */
    protected function getErrorRates()
    {
        return [
            '2xx' => 89.5,
            '4xx' => 8.2,
            '5xx' => 2.3,
        ];
    }

    /**
     * Get API response times.
     */
    protected function getResponseTimes()
    {
        return [
            'average' => 245, // ms
            'p95' => 450,
            'p99' => 890,
        ];
    }
}