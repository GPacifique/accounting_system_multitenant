<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Equipment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'brand',
        'model',
        'serial_number',
        'equipment_type',
        'description',
        'location',
        'purchase_date',
        'purchase_price',
        'warranty_expiry',
        'status',
        'maintenance_notes',
        'last_maintenance_date',
        'next_maintenance_due',
        'usage_hours',
        'maintenance_schedule',
        'manufacturer_contact',
        'safety_instructions',
        'image',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_due' => 'date',
        'purchase_price' => 'decimal:2',
        'usage_hours' => 'integer',
        'maintenance_schedule' => 'array',
    ];

    // Equipment type constants
    const TYPE_CARDIO = 'cardio';
    const TYPE_STRENGTH = 'strength';
    const TYPE_FREE_WEIGHTS = 'free_weights';
    const TYPE_FUNCTIONAL = 'functional';
    const TYPE_ACCESSORIES = 'accessories';
    const TYPE_POOL = 'pool';
    const TYPE_SAFETY = 'safety';
    const TYPE_CLEANING = 'cleaning';
    const TYPE_AUDIO_VISUAL = 'audio_visual';
    const TYPE_OTHER = 'other';

    // Status constants
    const STATUS_OPERATIONAL = 'operational';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_OUT_OF_ORDER = 'out_of_order';
    const STATUS_RETIRED = 'retired';

    /**
     * Check if equipment is operational
     */
    public function isOperational()
    {
        return $this->status === self::STATUS_OPERATIONAL;
    }

    /**
     * Check if equipment needs maintenance
     */
    public function needsMaintenance()
    {
        return $this->next_maintenance_due && $this->next_maintenance_due->isPast();
    }

    /**
     * Check if warranty is still valid
     */
    public function isUnderWarranty()
    {
        return $this->warranty_expiry && $this->warranty_expiry->isFuture();
    }

    /**
     * Get days until next maintenance
     */
    public function getDaysUntilMaintenanceAttribute()
    {
        if (!$this->next_maintenance_due) return null;
        return now()->diffInDays($this->next_maintenance_due, false);
    }

    /**
     * Get equipment age in years
     */
    public function getAgeInYearsAttribute()
    {
        if (!$this->purchase_date) return null;
        return $this->purchase_date->diffInYears(now());
    }

    /**
     * Schedule next maintenance
     */
    public function scheduleNextMaintenance($days = 30)
    {
        $this->update([
            'next_maintenance_due' => now()->addDays($days)
        ]);
    }

    /**
     * Mark as completed maintenance
     */
    public function completeMaintenance($notes = null, $nextMaintenanceDays = 30)
    {
        $this->update([
            'last_maintenance_date' => now(),
            'next_maintenance_due' => now()->addDays($nextMaintenanceDays),
            'status' => self::STATUS_OPERATIONAL,
            'maintenance_notes' => $notes ? $this->maintenance_notes . "\n" . now()->format('Y-m-d') . ": " . $notes : $this->maintenance_notes,
        ]);
    }

    /**
     * Mark as out of order
     */
    public function markOutOfOrder($reason = null)
    {
        $this->update([
            'status' => self::STATUS_OUT_OF_ORDER,
            'maintenance_notes' => $this->maintenance_notes . "\n" . now()->format('Y-m-d') . ": Out of order - " . ($reason ?? 'No reason provided'),
        ]);
    }

    /**
     * Add usage hours
     */
    public function addUsageHours($hours)
    {
        $this->increment('usage_hours', $hours);
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function maintenanceTasks()
    {
        return $this->tasks()->where('task_type', 'maintenance');
    }

    /**
     * Scopes
     */
    public function scopeOperational($query)
    {
        return $query->where('status', self::STATUS_OPERATIONAL);
    }

    public function scopeNeedsMaintenance($query)
    {
        return $query->where('next_maintenance_due', '<=', now());
    }

    public function scopeOutOfOrder($query)
    {
        return $query->where('status', self::STATUS_OUT_OF_ORDER);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('equipment_type', $type);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeUnderWarranty($query)
    {
        return $query->where('warranty_expiry', '>=', now());
    }

    public function scopeExpiredWarranty($query)
    {
        return $query->where('warranty_expiry', '<', now());
    }

    /**
     * Get equipment by location for display
     */
    public static function getByLocation($tenantId)
    {
        return static::where('tenant_id', $tenantId)
                    ->orderBy('location')
                    ->orderBy('equipment_type')
                    ->orderBy('name')
                    ->get()
                    ->groupBy('location');
    }

    /**
     * Get maintenance summary
     */
    public function getMaintenanceSummaryAttribute()
    {
        return [
            'last_maintenance' => $this->last_maintenance_date?->format('Y-m-d'),
            'next_due' => $this->next_maintenance_due?->format('Y-m-d'),
            'days_until_due' => $this->days_until_maintenance,
            'is_overdue' => $this->needsMaintenance(),
            'total_tasks' => $this->maintenanceTasks()->count(),
        ];
    }
}