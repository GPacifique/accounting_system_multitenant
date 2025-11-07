<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Membership extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'member_id',
        'membership_type',
        'start_date',
        'end_date',
        'price',
        'status',
        'payment_frequency',
        'auto_renewal',
        'benefits',
        'restrictions',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'auto_renewal' => 'boolean',
        'benefits' => 'array',
        'restrictions' => 'array',
    ];

    // Membership type constants
    const TYPE_BASIC = 'basic';
    const TYPE_PREMIUM = 'premium';
    const TYPE_VIP = 'vip';
    const TYPE_STUDENT = 'student';
    const TYPE_SENIOR = 'senior';
    const TYPE_FAMILY = 'family';
    const TYPE_CORPORATE = 'corporate';
    const TYPE_DAY_PASS = 'day_pass';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PENDING = 'pending';

    // Payment frequency constants
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_SEMI_ANNUAL = 'semi_annual';
    const FREQUENCY_ANNUAL = 'annual';
    const FREQUENCY_ONE_TIME = 'one_time';

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'membership_id');
    }

    /**
     * Check if membership is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && !$this->isExpired();
    }

    /**
     * Check if membership is expired
     */
    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Get days remaining until expiration
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) return null;
        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Check if membership expires within given days
     */
    public function expiresWithin($days)
    {
        return $this->end_date && $this->end_date->lte(now()->addDays($days));
    }

    /**
     * Renew membership
     */
    public function renew($duration = null, $price = null)
    {
        $newEndDate = $this->end_date ? $this->end_date : now();
        
        // Default duration based on payment frequency
        if (!$duration) {
            $duration = match($this->payment_frequency) {
                self::FREQUENCY_MONTHLY => 30,
                self::FREQUENCY_QUARTERLY => 90,
                self::FREQUENCY_SEMI_ANNUAL => 180,
                self::FREQUENCY_ANNUAL => 365,
                default => 30
            };
        }

        $this->update([
            'end_date' => $newEndDate->addDays($duration),
            'status' => self::STATUS_ACTIVE,
            'price' => $price ?? $this->price,
        ]);

        return $this;
    }

    /**
     * Suspend membership
     */
    public function suspend($reason = null)
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'notes' => $this->notes . "\nSuspended: " . ($reason ?? 'No reason provided') . ' on ' . now()->format('Y-m-d'),
        ]);
    }

    /**
     * Cancel membership
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $this->notes . "\nCancelled: " . ($reason ?? 'No reason provided') . ' on ' . now()->format('Y-m-d'),
        ]);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('membership_type', $type);
    }

    public function scopeAutoRenewal($query)
    {
        return $query->where('auto_renewal', true);
    }

    /**
     * Get total revenue for this membership
     */
    public function getTotalRevenueAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    /**
     * Get membership duration in days
     */
    public function getDurationDaysAttribute()
    {
        if (!$this->start_date || !$this->end_date) return null;
        return $this->start_date->diffInDays($this->end_date);
    }
}