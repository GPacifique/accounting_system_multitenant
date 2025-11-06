<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Ensure tenant_id is set in the data array for multi-tenant models
     */
    protected function ensureTenantId(array $data): array
    {
        // If tenant_id is already set, return as is
        if (isset($data['tenant_id'])) {
            return $data;
        }

        // Try to get tenant_id from current tenant context
        if (app()->bound('currentTenant')) {
            $currentTenant = app('currentTenant');
            if ($currentTenant) {
                $data['tenant_id'] = $currentTenant->id;
                return $data;
            }
        }
        
        // Fallback: get tenant_id from authenticated user
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->current_tenant_id) {
                $data['tenant_id'] = $user->current_tenant_id;
            }
        }

        return $data;
    }
}
