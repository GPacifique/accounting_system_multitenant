<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserInvitation;
use App\Models\User;
use App\Models\BusinessAdminPermission;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BusinessAdminController extends Controller
{
    /**
     * Invite a user to the current tenant
     */
    public function inviteUser(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        // Validate admin has permission to invite users
        if (!$admin->hasBusinessPermission('invite_users', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied',
                'message' => 'You do not have permission to invite users'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|string|in:employee,manager,accountant',
            'department' => 'nullable|string|max:255',
            'expires_in_days' => 'integer|min:1|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check tenant user limits
        if (!$this->checkUserLimits($tenant)) {
            return response()->json([
                'error' => 'User limit exceeded',
                'message' => 'Your current plan does not allow more users'
            ], 409);
        }

        $expiresAt = Carbon::now()->addDays($request->input('expires_in_days', 7));

        $invitation = UserInvitation::create([
            'tenant_id' => $tenant->id,
            'invited_by' => $admin->id,
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'role' => $request->input('role'),
            'department' => $request->input('department'),
            'token' => Str::random(64),
            'expires_at' => $expiresAt,
        ]);

        // Send invitation email (implement mail service)
        // Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        // Log the invitation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'action' => 'user_invited',
            'description' => "Invited user: {$invitation->email}",
            'metadata' => [
                'invitation_id' => $invitation->id,
                'invited_email' => $invitation->email,
                'role' => $invitation->role,
            ],
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'User invitation sent successfully',
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'role' => $invitation->role,
                'expires_at' => $invitation->expires_at->toISOString(),
                'status' => $invitation->status,
            ]
        ], 201);
    }

    /**
     * List pending invitations
     */
    public function listInvitations(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('invite_users', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $invitations = UserInvitation::where('tenant_id', $tenant->id)
            ->with(['invitedBy:id,first_name,last_name,email'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'invitations' => $invitations->items(),
            'pagination' => [
                'current_page' => $invitations->currentPage(),
                'total_pages' => $invitations->lastPage(),
                'per_page' => $invitations->perPage(),
                'total' => $invitations->total(),
            ]
        ]);
    }

    /**
     * Cancel a pending invitation
     */
    public function cancelInvitation(Request $request, int $invitationId): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('invite_users', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $invitation = UserInvitation::where('tenant_id', $tenant->id)
            ->where('id', $invitationId)
            ->first();

        if (!$invitation) {
            return response()->json([
                'error' => 'Invitation not found'
            ], 404);
        }

        if ($invitation->status !== 'pending') {
            return response()->json([
                'error' => 'Cannot cancel invitation',
                'message' => 'Only pending invitations can be cancelled'
            ], 409);
        }

        $invitation->update(['status' => 'cancelled']);

        // Log the cancellation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'action' => 'invitation_cancelled',
            'description' => "Cancelled invitation for: {$invitation->email}",
            'metadata' => [
                'invitation_id' => $invitation->id,
                'invited_email' => $invitation->email,
            ],
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Invitation cancelled successfully'
        ]);
    }

    /**
     * List tenant users
     */
    public function listUsers(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('manage_users', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status', 'active');

        $query = User::whereHas('tenants', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->whereHas('tenants', function ($q) use ($tenant, $role) {
                $q->where('tenant_id', $tenant->id)
                  ->where('role', $role);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $users = $query->with(['tenants' => function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        }])->paginate(20);

        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total_pages' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, int $userId): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('assign_roles', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:employee,manager,accountant,business_admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::whereHas('tenants', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        // Update user role in tenant relationship
        $user->tenants()->updateExistingPivot($tenant->id, [
            'role' => $request->input('role')
        ]);

        // Log the role change
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'action' => 'role_updated',
            'description' => "Updated role for user: {$user->email}",
            'metadata' => [
                'target_user_id' => $user->id,
                'new_role' => $request->input('role'),
            ],
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'User role updated successfully'
        ]);
    }

    /**
     * Deactivate user
     */
    public function deactivateUser(Request $request, int $userId): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('manage_users', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $user = User::whereHas('tenants', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        // Cannot deactivate yourself
        if ($user->id === $admin->id) {
            return response()->json([
                'error' => 'Cannot deactivate yourself'
            ], 409);
        }

        $user->update(['status' => 'inactive']);

        // Log the deactivation
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'action' => 'user_deactivated',
            'description' => "Deactivated user: {$user->email}",
            'metadata' => [
                'target_user_id' => $user->id,
            ],
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'User deactivated successfully'
        ]);
    }

    /**
     * Grant business admin permission
     */
    public function grantPermission(Request $request, int $userId): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('manage_permissions', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'permission' => 'required|string|in:invite_users,manage_users,assign_roles,view_reports,manage_settings,manage_billing,export_data,manage_integrations,view_audit_logs,manage_permissions,backup_data,manage_api_keys',
            'constraints' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::whereHas('tenants', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        $user->grantBusinessPermission(
            $request->input('permission'),
            $tenant->id,
            $request->input('constraints', [])
        );

        return response()->json([
            'message' => 'Permission granted successfully'
        ]);
    }

    /**
     * Check tenant user limits
     */
    protected function checkUserLimits($tenant): bool
    {
        if (!$tenant->max_users) {
            return true; // No limit set
        }

        $currentUsers = User::whereHas('tenants', function ($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->count();

        return $currentUsers < $tenant->max_users;
    }
}