<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\BusinessAdminController;
use App\Http\Controllers\Api\UserInvitationController;

/*
|--------------------------------------------------------------------------
| Multi-Tenant API Routes
|--------------------------------------------------------------------------
|
| Here we define the API routes for our multi-tenant application.
| All routes require tenant resolution middleware.
|
*/

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Tenant registration (self-service)
    Route::post('/tenants', [TenantController::class, 'store'])
        ->name('api.tenants.store');
    
    // Accept user invitation (public link)
    Route::post('/invitations/{token}/accept', [UserInvitationController::class, 'accept'])
        ->name('api.invitations.accept');
});

// Tenant-scoped routes (require tenant resolution)
Route::prefix('v1')->middleware([
    'resolve.tenant',
    'tenant.scope',
    'tenant.security:default'
])->group(function () {
    
    // Authentication required routes
    Route::middleware('auth:api')->group(function () {
        
        // Current tenant information
        Route::get('/tenant', [TenantController::class, 'show'])
            ->name('api.tenant.show');
        
        // Tenant statistics (business admin permission required)
        Route::get('/tenant/statistics', [TenantController::class, 'statistics'])
            ->name('api.tenant.statistics');
        
        // Update tenant settings (business admin permission required)
        Route::put('/tenant', [TenantController::class, 'update'])
            ->name('api.tenant.update');
        
        // Business Admin routes
        Route::prefix('admin')->middleware('tenant.security:admin')->group(function () {
            
            // User management
            Route::get('/users', [BusinessAdminController::class, 'listUsers'])
                ->name('api.mt.admin.users.index');
            
            Route::put('/users/{user}/role', [BusinessAdminController::class, 'updateUserRole'])
                ->name('api.mt.admin.users.role');
            
            Route::post('/users/{user}/deactivate', [BusinessAdminController::class, 'deactivateUser'])
                ->name('api.mt.admin.users.deactivate');
            
            Route::post('/users/{user}/permissions', [BusinessAdminController::class, 'grantPermission'])
                ->name('api.mt.admin.users.permissions.grant');
            
            // User invitations
            Route::post('/invitations', [BusinessAdminController::class, 'inviteUser'])
                ->name('api.mt.admin.invitations.store');
            
            Route::get('/invitations', [BusinessAdminController::class, 'listInvitations'])
                ->name('api.mt.admin.invitations.index');
            
            Route::delete('/invitations/{invitation}', [BusinessAdminController::class, 'cancelInvitation'])
                ->name('api.mt.admin.invitations.cancel');
        });
        
        // User profile routes
        Route::get('/profile', function (Request $request) {
            $user = $request->user();
            $tenant = $request->attributes->get('tenant');
            
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'tenant_role' => $user->getTenantRole($tenant->id),
                    'business_permissions' => $user->getBusinessPermissions($tenant->id),
                ]
            ]);
        })->name('api.mt.profile.show');
        
        // Update user profile
        Route::put('/profile', function (Request $request) {
            $user = $request->user();
            
            $validator = validator($request->all(), [
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $user->update($request->only(['first_name', 'last_name', 'phone']));
            
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            ]);
        })->name('api.mt.profile.update');
    });
});

// Super Admin routes (landlord context, no tenant scoping)
Route::prefix('v1/admin')->middleware([
    'auth:api',
    'tenant.security:admin'
])->group(function () {
    
    // Global tenant management (super admin only)
    Route::get('/tenants', function (Request $request) {
        $user = $request->user();
        
        if (!$user->isSuperAdmin()) {
            return response()->json(['error' => 'Permission denied'], 403);
        }
        
        $tenants = \App\Models\Tenant::with(['users' => function ($q) {
            $q->wherePivot('role', 'business_admin');
        }])->paginate(20);
        
        return response()->json([
            'tenants' => $tenants->items(),
            'pagination' => [
                'current_page' => $tenants->currentPage(),
                'total_pages' => $tenants->lastPage(),
                'per_page' => $tenants->perPage(),
                'total' => $tenants->total(),
            ]
        ]);
    })->name('api.mt.admin.tenants.index');
    
    // Suspend tenant
    Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])
        ->name('api.mt.admin.tenants.suspend');
    
    // Reactivate tenant
    Route::post('/tenants/{tenant}/reactivate', [TenantController::class, 'reactivate'])
        ->name('api.mt.admin.tenants.reactivate');
    
    // Global statistics
    Route::get('/statistics', function (Request $request) {
        $user = $request->user();
        
        if (!$user->isSuperAdmin()) {
            return response()->json(['error' => 'Permission denied'], 403);
        }
        
        $stats = [
            'total_tenants' => \App\Models\Tenant::count(),
            'active_tenants' => \App\Models\Tenant::where('status', 'active')->count(),
            'trial_tenants' => \App\Models\Tenant::whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '>', now())->count(),
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('status', 'active')->count(),
        ];
        
        return response()->json(['statistics' => $stats]);
    })->name('api.mt.admin.statistics');
});

// Health check endpoint (no authentication)
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
    ]);
})->name('api.health');