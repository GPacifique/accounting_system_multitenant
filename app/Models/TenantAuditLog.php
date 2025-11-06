<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class TenantAuditLog extends Model
{
    use HasFactory;

    // Only created_at timestamp needed
    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'metadata',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_ACCESSED = 'accessed';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_BACKUP = 'backup';
    const ACTION_RESTORE = 'restore';

    /**
     * The tenant this log belongs to.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new audit log entry.
     */
    public static function log(
        int $tenantId,
        string $action,
        string $resourceType = null,
        int $resourceId = null,
        array $oldValues = null,
        array $newValues = null,
        string $description = null,
        array $metadata = []
    ): self {
        return self::create([
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description ?: self::generateDescription($action, $resourceType, $resourceId),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log model creation.
     */
    public static function logCreated(Model $model, array $metadata = []): self
    {
        return self::log(
            $model->tenant_id ?? app('currentTenant')?->id,
            self::ACTION_CREATED,
            get_class($model),
            $model->id,
            null,
            $model->getAttributes(),
            null,
            $metadata
        );
    }

    /**
     * Log model update.
     */
    public static function logUpdated(Model $model, array $originalValues, array $metadata = []): self
    {
        return self::log(
            $model->tenant_id ?? app('currentTenant')?->id,
            self::ACTION_UPDATED,
            get_class($model),
            $model->id,
            $originalValues,
            $model->getChanges(),
            null,
            $metadata
        );
    }

    /**
     * Log model deletion.
     */
    public static function logDeleted(Model $model, array $metadata = []): self
    {
        return self::log(
            $model->tenant_id ?? app('currentTenant')?->id,
            self::ACTION_DELETED,
            get_class($model),
            $model->id,
            $model->getAttributes(),
            null,
            null,
            $metadata
        );
    }

    /**
     * Log user login.
     */
    public static function logLogin(int $tenantId, array $metadata = []): self
    {
        return self::log(
            $tenantId,
            self::ACTION_LOGIN,
            User::class,
            Auth::id(),
            null,
            null,
            'User logged in',
            $metadata
        );
    }

    /**
     * Log user logout.
     */
    public static function logLogout(int $tenantId, array $metadata = []): self
    {
        return self::log(
            $tenantId,
            self::ACTION_LOGOUT,
            User::class,
            Auth::id(),
            null,
            null,
            'User logged out',
            $metadata
        );
    }

    /**
     * Log data export.
     */
    public static function logExport(int $tenantId, string $exportType, array $metadata = []): self
    {
        return self::log(
            $tenantId,
            self::ACTION_EXPORT,
            null,
            null,
            null,
            null,
            "Exported {$exportType} data",
            array_merge(['export_type' => $exportType], $metadata)
        );
    }

    /**
     * Generate human-readable description.
     */
    protected static function generateDescription(string $action, string $resourceType = null, int $resourceId = null): string
    {
        $userName = Auth::user()?->name ?? 'System';
        $resourceName = $resourceType ? class_basename($resourceType) : 'resource';
        
        $descriptions = [
            self::ACTION_CREATED => "Created {$resourceName}" . ($resourceId ? " #{$resourceId}" : ''),
            self::ACTION_UPDATED => "Updated {$resourceName}" . ($resourceId ? " #{$resourceId}" : ''),
            self::ACTION_DELETED => "Deleted {$resourceName}" . ($resourceId ? " #{$resourceId}" : ''),
            self::ACTION_ACCESSED => "Accessed {$resourceName}" . ($resourceId ? " #{$resourceId}" : ''),
            self::ACTION_LOGIN => 'User logged in',
            self::ACTION_LOGOUT => 'User logged out',
            self::ACTION_EXPORT => 'Exported data',
            self::ACTION_IMPORT => 'Imported data',
            self::ACTION_BACKUP => 'Created backup',
            self::ACTION_RESTORE => 'Restored data',
        ];

        return $descriptions[$action] ?? "Performed {$action}";
    }

    /**
     * Get action color class.
     */
    public function getActionColorClass(): string
    {
        return match($this->action) {
            self::ACTION_CREATED => 'bg-green-100 text-green-800',
            self::ACTION_UPDATED => 'bg-blue-100 text-blue-800',
            self::ACTION_DELETED => 'bg-red-100 text-red-800',
            self::ACTION_ACCESSED => 'bg-gray-100 text-gray-800',
            self::ACTION_LOGIN => 'bg-purple-100 text-purple-800',
            self::ACTION_LOGOUT => 'bg-yellow-100 text-yellow-800',
            self::ACTION_EXPORT => 'bg-indigo-100 text-indigo-800',
            self::ACTION_IMPORT => 'bg-teal-100 text-teal-800',
            self::ACTION_BACKUP => 'bg-orange-100 text-orange-800',
            self::ACTION_RESTORE => 'bg-pink-100 text-pink-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get action icon.
     */
    public function getActionIcon(): string
    {
        return match($this->action) {
            self::ACTION_CREATED => 'fas fa-plus-circle',
            self::ACTION_UPDATED => 'fas fa-edit',
            self::ACTION_DELETED => 'fas fa-trash',
            self::ACTION_ACCESSED => 'fas fa-eye',
            self::ACTION_LOGIN => 'fas fa-sign-in-alt',
            self::ACTION_LOGOUT => 'fas fa-sign-out-alt',
            self::ACTION_EXPORT => 'fas fa-download',
            self::ACTION_IMPORT => 'fas fa-upload',
            self::ACTION_BACKUP => 'fas fa-save',
            self::ACTION_RESTORE => 'fas fa-undo',
            default => 'fas fa-info-circle',
        };
    }

    /**
     * Get risk level for the action.
     */
    public function getRiskLevel(): string
    {
        return match($this->action) {
            self::ACTION_DELETED => 'high',
            self::ACTION_EXPORT => 'high',
            self::ACTION_RESTORE => 'high',
            self::ACTION_BACKUP => 'medium',
            self::ACTION_UPDATED => 'medium',
            self::ACTION_IMPORT => 'medium',
            self::ACTION_LOGIN => 'low',
            self::ACTION_LOGOUT => 'low',
            self::ACTION_CREATED => 'low',
            self::ACTION_ACCESSED => 'low',
            default => 'low',
        };
    }

    /**
     * Get changes summary.
     */
    public function getChangesSummary(): string
    {
        if (!$this->old_values && !$this->new_values) {
            return 'No changes recorded';
        }

        $changes = [];
        
        if ($this->action === self::ACTION_UPDATED && $this->old_values && $this->new_values) {
            foreach ($this->new_values as $field => $newValue) {
                $oldValue = $this->old_values[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = "{$field}: '{$oldValue}' → '{$newValue}'";
                }
            }
        }

        return $changes ? implode(', ', $changes) : 'No specific changes';
    }

    /**
     * Scope for recent logs.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific action.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific resource.
     */
    public function scopeResource($query, string $resourceType, int $resourceId = null)
    {
        $query = $query->where('resource_type', $resourceType);
        
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        
        return $query;
    }
}