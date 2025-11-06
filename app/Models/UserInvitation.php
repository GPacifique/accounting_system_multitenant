<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserInvitation extends Model
{
    protected $fillable = [
        'tenant_id',
        'invited_by',
        'email',
        'role',
        'is_admin',
        'token',
        'permissions',
        'expires_at',
        'metadata',
        'status'
    ];

    protected $casts = [
        'permissions' => 'array',
        'metadata' => 'array',
        'is_admin' => 'boolean',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public const ROLES = [
        'admin' => 'Administrator',
        'manager' => 'Manager', 
        'accountant' => 'Accountant',
        'user' => 'User'
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled'
    ];

    /**
     * Generate invitation token and set expiration
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invitation) {
            if (!$invitation->token) {
                $invitation->token = Str::random(64);
            }
            
            if (!$invitation->expires_at) {
                // Default: 7 days expiration
                $invitation->expires_at = Carbon::now()->addDays(7);
            }
        });
    }

    /**
     * Relationships
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Check if invitation is valid
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && $this->expires_at > now();
    }

    /**
     * Check if invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Accept the invitation
     */
    public function accept(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Add user to tenant with specified role
        $user->addToTenant($this->tenant_id, $this->role, $this->is_admin);
        
        // Apply specific permissions if any
        if ($this->permissions) {
            foreach ($this->permissions as $permission => $constraints) {
                BusinessAdminPermission::create([
                    'user_id' => $user->id,
                    'tenant_id' => $this->tenant_id,
                    'permission' => $permission,
                    'constraints' => $constraints,
                    'granted_at' => now(),
                ]);
            }
        }

        // Mark as accepted
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Log the acceptance
        AuditLog::create([
            'tenant_id' => $this->tenant_id,
            'user_id' => $user->id,
            'action' => 'invitation_accepted',
            'description' => "User {$user->name} accepted invitation to join tenant as {$this->role}",
            'metadata' => [
                'invitation_id' => $this->id,
                'role' => $this->role,
                'is_admin' => $this->is_admin,
            ],
            'severity' => 'medium',
        ]);

        return true;
    }

    /**
     * Cancel the invitation
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Scope for valid invitations
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope for expired invitations
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '<=', now());
    }
}