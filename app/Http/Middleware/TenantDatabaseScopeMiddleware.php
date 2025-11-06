<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;

class TenantDatabaseScopeMiddleware
{
    /**
     * Handle an incoming request to enforce tenant database scoping
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = $request->attributes->get('tenant');
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context required',
                'message' => 'No tenant context available for this request'
            ], 400);
        }

        $this->enableTenantScoping($tenant);

        return $next($request);
    }

    /**
     * Enable automatic tenant scoping for all tenant-aware models
     */
    protected function enableTenantScoping(Tenant $tenant): void
    {
        // Set global scope for all models using BelongsToTenant trait
        Model::addGlobalScope('tenant', function (Builder $builder) use ($tenant) {
            $model = $builder->getModel();
            
            // Only apply to models that use BelongsToTenant trait
            if (in_array('App\Models\Traits\BelongsToTenant', class_uses($model))) {
                $builder->where($model->getTable() . '.tenant_id', $tenant->id);
            }
        });

        // Store tenant ID in application for use in model observers
        app()->instance('currentTenantId', $tenant->id);
    }
}