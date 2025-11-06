<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TenantDataService
{
    protected ?Tenant $currentTenant;
    protected ?User $currentUser;

    public function __construct()
    {
        $this->currentTenant = app()->bound('currentTenant') ? app('currentTenant') : null;
        $this->currentUser = Auth::user();
    }

    /**
     * Get data for the current tenant with optional caching.
     */
    public function getForCurrentTenant(string $modelClass, array $relations = [], int $cacheTtl = 0): Collection
    {
        $cacheKey = $this->generateCacheKey($modelClass, $relations);
        
        if ($cacheTtl > 0 && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $query = $modelClass::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Add tenant scope if model supports it
        if ($this->modelSupportsTenancy($modelClass)) {
            $query = $this->applyTenantScope($query);
        }

        $data = $query->get();

        if ($cacheTtl > 0) {
            Cache::put($cacheKey, $data, now()->addMinutes($cacheTtl));
        }

        return $data;
    }

    /**
     * Get paginated data for the current tenant.
     */
    public function getPaginatedForCurrentTenant(
        string $modelClass, 
        int $perPage = 15, 
        array $relations = [], 
        array $filters = []
    ) {
        $query = $modelClass::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply tenant scope if model supports it
        if ($this->modelSupportsTenancy($modelClass)) {
            $query = $this->applyTenantScope($query);
        }

        // Apply additional filters
        $query = $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Get specific record by ID for current tenant.
     */
    public function findForCurrentTenant(string $modelClass, $id, array $relations = []): ?Model
    {
        $query = $modelClass::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply tenant scope if model supports it
        if ($this->modelSupportsTenancy($modelClass)) {
            $query = $this->applyTenantScope($query);
        }

        return $query->find($id);
    }

    /**
     * Get records for a specific tenant (admin use).
     */
    public function getForSpecificTenant(
        string $modelClass, 
        int $tenantId, 
        array $relations = [], 
        array $filters = []
    ): Collection {
        $query = $modelClass::withoutGlobalScope('tenant');
        
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply specific tenant scope
        if ($this->modelSupportsTenancy($modelClass)) {
            $query->where('tenant_id', $tenantId);
        }

        // Apply additional filters
        $query = $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get aggregated data across all tenants (super admin only).
     */
    public function getAggregatedData(string $modelClass, array $aggregations = []): array
    {
        $this->ensureSuperAdmin();

        $query = $modelClass::withoutGlobalScope('tenant');
        $results = [];

        foreach ($aggregations as $field => $operation) {
            switch ($operation) {
                case 'count':
                    $results[$field . '_count'] = $query->count();
                    break;
                case 'sum':
                    $results[$field . '_sum'] = $query->sum($field);
                    break;
                case 'avg':
                    $results[$field . '_avg'] = $query->avg($field);
                    break;
                case 'max':
                    $results[$field . '_max'] = $query->max($field);
                    break;
                case 'min':
                    $results[$field . '_min'] = $query->min($field);
                    break;
            }
        }

        return $results;
    }

    /**
     * Get tenant-specific statistics.
     */
    public function getTenantStatistics(int $tenantId = null): array
    {
        $targetTenantId = $tenantId ?? $this->currentTenant?->id;
        
        if (!$targetTenantId) {
            throw new \Exception('No tenant context available');
        }

        $cacheKey = "tenant_stats_{$targetTenantId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $stats = [
            'tenant_id' => $targetTenantId,
            'users_count' => $this->getUsersCountForTenant($targetTenantId),
            'active_users_count' => $this->getActiveUsersCountForTenant($targetTenantId),
            'last_activity' => $this->getLastActivityForTenant($targetTenantId),
            'storage_used' => $this->getStorageUsedForTenant($targetTenantId),
        ];

        // Cache for 10 minutes
        Cache::put($cacheKey, $stats, now()->addMinutes(10));

        return $stats;
    }

    /**
     * Get cross-tenant analytics (super admin only).
     */
    public function getCrossTenantAnalytics(): array
    {
        $this->ensureSuperAdmin();

        $cacheKey = 'cross_tenant_analytics';
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $analytics = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_users' => User::withoutGlobalScope('tenant')->count(),
            'tenants_by_plan' => $this->getTenantsByPlan(),
            'monthly_signups' => $this->getMonthlySignups(),
            'usage_statistics' => $this->getUsageStatistics(),
        ];

        // Cache for 30 minutes
        Cache::put($cacheKey, $analytics, now()->addMinutes(30));

        return $analytics;
    }

    /**
     * Search across tenant data with proper scoping.
     */
    public function searchInTenant(
        string $modelClass, 
        string $searchTerm, 
        array $searchFields = [], 
        array $relations = [],
        int $limit = 50
    ): Collection {
        $query = $modelClass::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply tenant scope if model supports it
        if ($this->modelSupportsTenancy($modelClass)) {
            $query = $this->applyTenantScope($query);
        }

        // Apply search filters
        if (!empty($searchFields)) {
            $query->where(function ($q) use ($searchFields, $searchTerm) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
                }
            });
        }

        return $query->limit($limit)->get();
    }

    /**
     * Bulk operations with tenant awareness.
     */
    public function bulkUpdateForTenant(
        string $modelClass, 
        array $ids, 
        array $updateData
    ): int {
        $query = $modelClass::query()->whereIn('id', $ids);
        
        // Apply tenant scope if model supports it
        if ($this->modelSupportsTenancy($modelClass)) {
            $query = $this->applyTenantScope($query);
        }

        return $query->update($updateData);
    }

    /**
     * Apply tenant scope to query.
     */
    protected function applyTenantScope(Builder $query): Builder
    {
        if ($this->currentTenant) {
            return $query->where('tenant_id', $this->currentTenant->id);
        }
        
        return $query;
    }

    /**
     * Apply additional filters to query.
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Check if model supports tenancy.
     */
    protected function modelSupportsTenancy(string $modelClass): bool
    {
        return in_array('App\Traits\BelongsToTenant', class_uses_recursive($modelClass));
    }

    /**
     * Generate cache key for tenant data.
     */
    protected function generateCacheKey(string $modelClass, array $relations = []): string
    {
        $tenantId = $this->currentTenant?->id ?? 'no_tenant';
        $relationsKey = !empty($relations) ? '_' . implode('_', $relations) : '';
        
        return "tenant_{$tenantId}_" . class_basename($modelClass) . $relationsKey;
    }

    /**
     * Ensure current user is super admin.
     */
    protected function ensureSuperAdmin(): void
    {
        if (!$this->currentUser || !$this->currentUser->is_super_admin) {
            throw new \Exception('Super admin access required');
        }
    }

    /**
     * Get users count for specific tenant.
     */
    protected function getUsersCountForTenant(int $tenantId): int
    {
        return User::withoutGlobalScope('tenant')
                   ->whereHas('tenants', function ($query) use ($tenantId) {
                       $query->where('tenant_id', $tenantId);
                   })
                   ->count();
    }

    /**
     * Get active users count for specific tenant.
     */
    protected function getActiveUsersCountForTenant(int $tenantId): int
    {
        return User::withoutGlobalScope('tenant')
                   ->where('status', 'active')
                   ->whereHas('tenants', function ($query) use ($tenantId) {
                       $query->where('tenant_id', $tenantId);
                   })
                   ->count();
    }

    /**
     * Get last activity for specific tenant.
     */
    protected function getLastActivityForTenant(int $tenantId): ?string
    {
        // This would depend on your activity logging implementation
        // For now, return a placeholder
        return now()->subDays(rand(1, 30))->toDateTimeString();
    }

    /**
     * Get storage used for specific tenant.
     */
    protected function getStorageUsedForTenant(int $tenantId): string
    {
        // This would calculate actual storage usage
        // For now, return a placeholder
        return number_format(rand(100, 5000), 2) . ' MB';
    }

    /**
     * Get tenants grouped by subscription plan.
     */
    protected function getTenantsByPlan(): array
    {
        return Tenant::selectRaw('subscription_plan, COUNT(*) as count')
                     ->groupBy('subscription_plan')
                     ->pluck('count', 'subscription_plan')
                     ->toArray();
    }

    /**
     * Get monthly tenant signups.
     */
    protected function getMonthlySignups(): array
    {
        return Tenant::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                     ->groupBy('year', 'month')
                     ->orderBy('year', 'desc')
                     ->orderBy('month', 'desc')
                     ->limit(12)
                     ->get()
                     ->map(function ($item) {
                         return [
                             'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                             'count' => $item->count
                         ];
                     })
                     ->toArray();
    }

    /**
     * Get usage statistics across all tenants.
     */
    protected function getUsageStatistics(): array
    {
        return [
            'avg_users_per_tenant' => $this->getUsersCountForTenant(0) / max(Tenant::count(), 1),
            'total_storage_used' => rand(1000, 50000) . ' GB', // Placeholder
            'api_requests_today' => rand(1000, 10000), // Placeholder
        ];
    }
}