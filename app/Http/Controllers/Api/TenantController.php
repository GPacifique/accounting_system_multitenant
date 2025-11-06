<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantController extends Controller
{
    /**
     * Create a new tenant (for super admins or self-registration)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain|regex:/^[a-zA-Z0-9\-]+$/',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'timezone' => 'required|string|in:' . implode(',', timezone_identifiers_list()),
            'currency' => 'required|string|size:3',
            'locale' => 'required|string|size:2',
            // Admin user details
            'admin_first_name' => 'required|string|max:255',
            'admin_last_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->input('name'),
            'domain' => $request->input('domain'),
            'contact_email' => $request->input('contact_email'),
            'contact_phone' => $request->input('contact_phone'),
            'description' => $request->input('description'),
            'timezone' => $request->input('timezone'),
            'currency' => $request->input('currency'),
            'locale' => $request->input('locale'),
            'status' => 'active',
            'max_users' => config('multitenancy.default_user_limit', 10),
            'trial_ends_at' => Carbon::now()->addDays(30), // 30-day trial
            'features' => [
                'accounting' => true,
                'invoicing' => true,
                'reporting' => true,
                'api_access' => false,
                'advanced_permissions' => false,
            ],
        ]);

        // Create admin user
        $adminUser = User::create([
            'first_name' => $request->input('admin_first_name'),
            'last_name' => $request->input('admin_last_name'),
            'email' => $request->input('admin_email'),
            'password' => Hash::make($request->input('admin_password')),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        // Associate admin with tenant
        $adminUser->tenants()->attach($tenant->id, [
            'role' => 'business_admin',
            'joined_at' => now(),
        ]);

        // Grant all business admin permissions
        $permissions = [
            'invite_users',
            'manage_users',
            'assign_roles',
            'view_reports',
            'manage_settings',
            'manage_billing',
            'export_data',
            'manage_integrations',
            'view_audit_logs',
            'manage_permissions',
            'backup_data',
            'manage_api_keys',
        ];

        foreach ($permissions as $permission) {
            $adminUser->grantBusinessPermission($permission, $tenant->id);
        }

        // Make tenant current
        $tenant->makeCurrent();

        // Log tenant creation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $adminUser->id,
            'action' => 'tenant_created',
            'description' => "Tenant created: {$tenant->name}",
            'metadata' => [
                'tenant_domain' => $tenant->domain,
                'admin_email' => $adminUser->email,
            ],
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'status' => $tenant->status,
                'trial_ends_at' => $tenant->trial_ends_at?->toISOString(),
            ],
            'admin_user' => [
                'id' => $adminUser->id,
                'name' => $adminUser->first_name . ' ' . $adminUser->last_name,
                'email' => $adminUser->email,
            ]
        ], 201);
    }

    /**
     * Get current tenant information
     */
    public function show(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context required'
            ], 400);
        }

        $user = $request->user();
        
        // Check if user can view tenant details
        if (!$user->belongsToTenant($tenant->id) && !$user->isSuperAdmin()) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        // Get user counts
        $userCounts = [
            'total' => $tenant->users()->count(),
            'active' => $tenant->users()->where('status', 'active')->count(),
            'pending_invitations' => $tenant->invitations()->where('status', 'pending')->count(),
        ];

        return response()->json([
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'contact_email' => $tenant->contact_email,
                'contact_phone' => $tenant->contact_phone,
                'description' => $tenant->description,
                'status' => $tenant->status,
                'timezone' => $tenant->timezone,
                'currency' => $tenant->currency,
                'locale' => $tenant->locale,
                'max_users' => $tenant->max_users,
                'trial_ends_at' => $tenant->trial_ends_at?->toISOString(),
                'features' => $tenant->features,
                'created_at' => $tenant->created_at->toISOString(),
                'user_counts' => $userCounts,
            ]
        ]);
    }

    /**
     * Update tenant information
     */
    public function update(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $user = $request->user();

        // Check if user can update tenant
        if (!$user->hasBusinessPermission('manage_settings', $tenant->id) && !$user->isSuperAdmin()) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'contact_email' => 'sometimes|required|email',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'timezone' => 'sometimes|required|string|in:' . implode(',', timezone_identifiers_list()),
            'currency' => 'sometimes|required|string|size:3',
            'locale' => 'sometimes|required|string|size:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldData = $tenant->only([
            'name', 'contact_email', 'contact_phone', 'description',
            'timezone', 'currency', 'locale'
        ]);

        $tenant->update($request->only([
            'name', 'contact_email', 'contact_phone', 'description',
            'timezone', 'currency', 'locale'
        ]));

        // Log the update
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'tenant_updated',
            'description' => "Updated tenant settings",
            'metadata' => [
                'old_data' => $oldData,
                'new_data' => $tenant->only([
                    'name', 'contact_email', 'contact_phone', 'description',
                    'timezone', 'currency', 'locale'
                ]),
            ],
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Tenant updated successfully',
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'contact_email' => $tenant->contact_email,
                'contact_phone' => $tenant->contact_phone,
                'description' => $tenant->description,
                'timezone' => $tenant->timezone,
                'currency' => $tenant->currency,
                'locale' => $tenant->locale,
            ]
        ]);
    }

    /**
     * Get tenant statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $user = $request->user();

        if (!$user->hasBusinessPermission('view_reports', $tenant->id) && !$user->isSuperAdmin()) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $timeFrame = $request->input('time_frame', '30_days');
        $startDate = match($timeFrame) {
            '7_days' => Carbon::now()->subDays(7),
            '30_days' => Carbon::now()->subDays(30),
            '90_days' => Carbon::now()->subDays(90),
            '1_year' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };

        // User statistics
        $userStats = [
            'total_users' => $tenant->users()->count(),
            'active_users' => $tenant->users()->where('status', 'active')->count(),
            'new_users' => $tenant->users()->where('created_at', '>=', $startDate)->count(),
            'pending_invitations' => $tenant->invitations()->where('status', 'pending')->count(),
        ];

        // Activity statistics
        $activityStats = [
            'total_actions' => AuditLog::where('tenant_id', $tenant->id)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'login_count' => AuditLog::where('tenant_id', $tenant->id)
                ->where('action', 'login')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'high_severity_events' => AuditLog::where('tenant_id', $tenant->id)
                ->where('severity', 'high')
                ->where('created_at', '>=', $startDate)
                ->count(),
        ];

        // System health
        $systemHealth = [
            'storage_used' => 0, // Implement based on your storage system
            'api_calls_today' => AuditLog::where('tenant_id', $tenant->id)
                ->where('action', 'api_call')
                ->whereDate('created_at', today())
                ->count(),
            'last_backup' => $tenant->last_backup_at?->toISOString(),
            'subscription_status' => $tenant->hasActiveSubscription() ? 'active' : 'trial',
        ];

        return response()->json([
            'time_frame' => $timeFrame,
            'period' => [
                'start_date' => $startDate->toISOString(),
                'end_date' => Carbon::now()->toISOString(),
            ],
            'statistics' => [
                'users' => $userStats,
                'activity' => $activityStats,
                'system' => $systemHealth,
            ]
        ]);
    }

    /**
     * Suspend tenant (super admin only)
     */
    public function suspend(Request $request, int $tenantId): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isSuperAdmin()) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $tenant = Tenant::findOrFail($tenantId);
        
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $tenant->update(['status' => 'suspended']);

        // Log the suspension
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'tenant_suspended',
            'description' => "Tenant suspended: {$request->input('reason')}",
            'metadata' => [
                'reason' => $request->input('reason'),
                'suspended_by' => $user->email,
            ],
            'severity' => 'high',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Tenant suspended successfully'
        ]);
    }

    /**
     * Reactivate tenant (super admin only)
     */
    public function reactivate(Request $request, int $tenantId): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isSuperAdmin()) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $tenant = Tenant::findOrFail($tenantId);
        $tenant->update(['status' => 'active']);

        // Log the reactivation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'tenant_reactivated',
            'description' => "Tenant reactivated",
            'metadata' => [
                'reactivated_by' => $user->email,
            ],
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Tenant reactivated successfully'
        ]);
    }
}