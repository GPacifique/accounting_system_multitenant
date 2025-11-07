<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Carbon\Carbon;

class FitnessClass extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'trainer_id',
        'name',
        'description',
        'class_type',
        'difficulty_level',
        'duration_minutes',
        'max_capacity',
        'current_capacity',
        'price_per_session',
        'recurring_schedule',
        'class_date',
        'start_time',
        'end_time',
        'location',
        'equipment_needed',
        'status',
        'notes',
        'is_recurring',
        'recurring_pattern',
    ];

    protected $casts = [
        'class_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price_per_session' => 'decimal:2',
        'duration_minutes' => 'integer',
        'max_capacity' => 'integer',
        'current_capacity' => 'integer',
        'equipment_needed' => 'array',
        'recurring_schedule' => 'array',
        'recurring_pattern' => 'array',
        'is_recurring' => 'boolean',
    ];

    // Class type constants
    const TYPE_YOGA = 'yoga';
    const TYPE_PILATES = 'pilates';
    const TYPE_ZUMBA = 'zumba';
    const TYPE_SPINNING = 'spinning';
    const TYPE_CROSSFIT = 'crossfit';
    const TYPE_AEROBICS = 'aerobics';
    const TYPE_STRENGTH = 'strength_training';
    const TYPE_HIIT = 'hiit';
    const TYPE_MARTIAL_ARTS = 'martial_arts';
    const TYPE_SWIMMING = 'swimming';

    // Difficulty level constants
    const DIFFICULTY_BEGINNER = 'beginner';
    const DIFFICULTY_INTERMEDIATE = 'intermediate';
    const DIFFICULTY_ADVANCED = 'advanced';
    const DIFFICULTY_ALL_LEVELS = 'all_levels';

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_POSTPONED = 'postponed';

    /**
     * Check if class is full
     */
    public function isFull()
    {
        return $this->current_capacity >= $this->max_capacity;
    }

    /**
     * Get available spots
     */
    public function getAvailableSpotsAttribute()
    {
        return $this->max_capacity - $this->current_capacity;
    }

    /**
     * Check if class is happening today
     */
    public function isToday()
    {
        return $this->class_date && $this->class_date->isToday();
    }

    /**
     * Check if class is upcoming
     */
    public function isUpcoming()
    {
        return $this->class_date && $this->class_date->isFuture();
    }

    /**
     * Get total revenue for this class
     */
    public function getTotalRevenueAttribute()
    {
        return $this->bookings()->where('status', 'confirmed')->sum('amount_paid');
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function bookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(Member::class, 'class_bookings')
                   ->withPivot(['booking_date', 'status', 'amount_paid'])
                   ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('class_date', '>=', now())
                    ->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('class_date', today());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('class_type', $type);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeWithAvailableSpots($query)
    {
        return $query->whereRaw('current_capacity < max_capacity');
    }

    public function scopeByTrainer($query, $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    /**
     * Add a member to this class
     */
    public function addMember(Member $member, $amountPaid = null)
    {
        if ($this->isFull()) {
            throw new \Exception('Class is at full capacity');
        }

        $booking = $this->bookings()->create([
            'member_id' => $member->id,
            'tenant_id' => $this->tenant_id,
            'booking_date' => now(),
            'status' => 'confirmed',
            'amount_paid' => $amountPaid ?? $this->price_per_session,
        ]);

        $this->increment('current_capacity');

        return $booking;
    }

    /**
     * Remove a member from this class
     */
    public function removeMember(Member $member)
    {
        $booking = $this->bookings()->where('member_id', $member->id)->first();
        
        if ($booking) {
            $booking->delete();
            $this->decrement('current_capacity');
            return true;
        }

        return false;
    }

    /**
     * Get class duration in human readable format
     */
    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}