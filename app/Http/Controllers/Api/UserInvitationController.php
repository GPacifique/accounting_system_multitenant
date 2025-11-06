<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserInvitation;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserInvitationController extends Controller
{
    /**
     * Accept a user invitation and create account
     */
    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json([
                'error' => 'Invalid invitation',
                'message' => 'Invitation not found or already used'
            ], 404);
        }

        if ($invitation->isExpired()) {
            return response()->json([
                'error' => 'Invitation expired',
                'message' => 'This invitation has expired'
            ], 410);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
            'accept_terms' => 'required|accepted',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already exists
        $existingUser = User::where('email', $invitation->email)->first();
        
        if ($existingUser) {
            // User exists, just add to tenant
            $user = $existingUser;
            
            // Check if already belongs to this tenant
            if ($user->belongsToTenant($invitation->tenant_id)) {
                return response()->json([
                    'error' => 'User already exists',
                    'message' => 'User is already a member of this organization'
                ], 409);
            }
        } else {
            // Create new user
            $user = User::create([
                'first_name' => $invitation->first_name,
                'last_name' => $invitation->last_name,
                'email' => $invitation->email,
                'password' => Hash::make($request->input('password')),
                'phone' => $request->input('phone'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
        }

        // Add user to tenant
        $user->tenants()->attach($invitation->tenant_id, [
            'role' => $invitation->role,
            'department' => $invitation->department,
            'joined_at' => now(),
        ]);

        // Apply any permissions from the invitation
        if ($invitation->permissions) {
            foreach ($invitation->permissions as $permission) {
                $user->grantBusinessPermission(
                    $permission['permission'],
                    $invitation->tenant_id,
                    $permission['constraints'] ?? []
                );
            }
        }

        // Mark invitation as accepted
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'accepted_by' => $user->id,
        ]);

        // Log the acceptance
        AuditLog::create([
            'tenant_id' => $invitation->tenant_id,
            'user_id' => $user->id,
            'action' => 'invitation_accepted',
            'description' => "User accepted invitation: {$user->email}",
            'metadata' => [
                'invitation_id' => $invitation->id,
                'role' => $invitation->role,
                'is_new_user' => !$existingUser,
            ],
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Generate API token for immediate login
        $token = $user->createToken('invitation-acceptance', [
            'tenant:' . $invitation->tenant_id
        ])->plainTextToken;

        return response()->json([
            'message' => 'Invitation accepted successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'role' => $invitation->role,
            ],
            'tenant' => [
                'id' => $invitation->tenant->id,
                'name' => $invitation->tenant->name,
                'domain' => $invitation->tenant->domain,
            ],
            'token' => $token,
            'is_new_user' => !$existingUser,
        ], 201);
    }

    /**
     * Get invitation details (for the acceptance form)
     */
    public function show(Request $request, string $token): JsonResponse
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('status', 'pending')
            ->with(['tenant:id,name,domain', 'invitedBy:id,first_name,last_name,email'])
            ->first();

        if (!$invitation) {
            return response()->json([
                'error' => 'Invalid invitation',
                'message' => 'Invitation not found or already used'
            ], 404);
        }

        if ($invitation->isExpired()) {
            return response()->json([
                'error' => 'Invitation expired',
                'message' => 'This invitation has expired'
            ], 410);
        }

        return response()->json([
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'first_name' => $invitation->first_name,
                'last_name' => $invitation->last_name,
                'role' => $invitation->role,
                'department' => $invitation->department,
                'expires_at' => $invitation->expires_at->toISOString(),
                'tenant' => [
                    'name' => $invitation->tenant->name,
                    'domain' => $invitation->tenant->domain,
                ],
                'invited_by' => [
                    'name' => $invitation->invitedBy->first_name . ' ' . $invitation->invitedBy->last_name,
                    'email' => $invitation->invitedBy->email,
                ],
                'created_at' => $invitation->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * Resend invitation email
     */
    public function resend(Request $request, int $invitationId): JsonResponse
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
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json([
                'error' => 'Invitation not found or already accepted'
            ], 404);
        }

        if ($invitation->isExpired()) {
            // Extend expiration
            $invitation->update([
                'expires_at' => Carbon::now()->addDays(7)
            ]);
        }

        // Send invitation email (implement mail service)
        // Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

        // Log the resend
        AuditLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $admin->id,
            'action' => 'invitation_resent',
            'description' => "Resent invitation to: {$invitation->email}",
            'metadata' => [
                'invitation_id' => $invitation->id,
            ],
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Invitation resent successfully'
        ]);
    }

    /**
     * Get invitation statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');
        $admin = $request->user();

        if (!$admin->hasBusinessPermission('view_reports', $tenant->id)) {
            return response()->json([
                'error' => 'Permission denied'
            ], 403);
        }

        $timeFrame = $request->input('time_frame', '30_days');
        $startDate = match($timeFrame) {
            '7_days' => Carbon::now()->subDays(7),
            '30_days' => Carbon::now()->subDays(30),
            '90_days' => Carbon::now()->subDays(90),
            default => Carbon::now()->subDays(30),
        };

        $stats = [
            'total_sent' => UserInvitation::where('tenant_id', $tenant->id)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'pending' => UserInvitation::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'accepted' => UserInvitation::where('tenant_id', $tenant->id)
                ->where('status', 'accepted')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'expired' => UserInvitation::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->where('expires_at', '<', now())
                ->where('created_at', '>=', $startDate)
                ->count(),
            'acceptance_rate' => 0,
        ];

        if ($stats['total_sent'] > 0) {
            $stats['acceptance_rate'] = round(($stats['accepted'] / $stats['total_sent']) * 100, 1);
        }

        return response()->json([
            'time_frame' => $timeFrame,
            'statistics' => $stats
        ]);
    }
}