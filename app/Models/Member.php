<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToTenant;
use Carbon\Carbon;

class Member extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'member_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'membership_type',
        'membership_start_date',
        'membership_end_date',
        'membership_status',
        'medical_conditions',
        'fitness_goals',
        'joined_at',
        'last_visit_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'membership_start_date' => 'date',
        'membership_end_date' => 'date',
        'joined_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'medical_conditions' => 'array',
        'fitness_goals' => 'array',
    ];

    // Membership status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_CANCELLED = 'cancelled';

    // Membership type constants
    const TYPE_BASIC = 'basic';
    const TYPE_PREMIUM = 'premium';
    const TYPE_VIP = 'vip';
    const TYPE_DAY_PASS = 'day_pass';
    const TYPE_STUDENT = 'student';

    /**
     * Get the member's full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if membership is active
     */
    public function isActiveMember()
    {
        return $this->membership_status === self::STATUS_ACTIVE 
            && $this->membership_end_date 
            && $this->membership_end_date->isFuture();
    }

    /**
     * Check if membership is expired
     */
    public function isMembershipExpired()
    {
        return $this->membership_end_date && $this->membership_end_date->isPast();
    }

    /**
     * Get days until membership expires
     */
    public function getDaysUntilExpirationAttribute()
    {
        if (!$this->membership_end_date) return null;
        return now()->diffInDays($this->membership_end_date, false);
    }

    /**
     * Get member's age
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function classBookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    public function personalTrainingSessions()
    {
        return $this->hasMany(PersonalTrainingSession::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'member_id');
    }

    public function checkIns()
    {
        return $this->hasMany(GymCheckIn::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('membership_status', self::STATUS_ACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('membership_status', self::STATUS_EXPIRED)
                    ->orWhere('membership_end_date', '<', now());
    }

    public function scopeExpiringWithin($query, $days = 30)
    {
        return $query->where('membership_end_date', '<=', now()->addDays($days))
                    ->where('membership_end_date', '>=', now());
    }

    public function scopeByMembershipType($query, $type)
    {
        return $query->where('membership_type', $type);
    }
}