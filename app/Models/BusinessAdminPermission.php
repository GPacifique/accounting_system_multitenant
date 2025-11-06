<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAdminPermission extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id', 
        'permission',
        'constraints',
        'granted_at',
        'expires_at'
    ];

    protected $casts = [
        'constraints' => 'array',
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Available business admin permissions
     */
    public const PERMISSIONS = [
        'invite_users' => 'Invite new users to the business',
        'manage_users' => 'Manage existing user accounts',
        'assign_roles' => 'Assign and modify user roles',
        'view_audit_logs' => 'View business activity logs',
        'manage_billing' => 'Manage billing and subscription',
        'export_data' => 'Export business data',
        'manage_integrations' => 'Manage third-party integrations',
        'configure_settings' => 'Configure business settings',
        'manage_projects' => 'Create and manage projects',
        'manage_clients' => 'Create and manage clients',
        'manage_finances' => 'Manage financial records',
        'manage_reports' => 'Generate and view reports',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if permission is currently valid
     */
    public function isValid(): bool
    {
        return !$this->expires_at || $this->expires_at > now();
    }

    /**
     * Check if permission has specific constraint
     */
    public function hasConstraint(string $key, $value = null): bool
    {
        if (!$this->constraints) {
            return false;
        }

        if ($value === null) {
            return array_key_exists($key, $this->constraints);
        }

        return ($this->constraints[$key] ?? null) === $value;
    }

    /**
     * Get constraint value
     */
    public function getConstraint(string $key, $default = null)
    {
        return $this->constraints[$key] ?? $default;
    }

    /**
     * Scope for valid permissions
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for specific permission
     */
    public function scopeForPermission($query, string $permission)
    {
        return $query->where('permission', $permission);
    }

    /**
     * Scope for user and tenant
     */
    public function scopeForUserInTenant($query, int $userId, int $tenantId)
    {
        return $query->where('user_id', $userId)
                    ->where('tenant_id', $tenantId);
    }
}