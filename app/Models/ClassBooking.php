<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class ClassBooking extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'member_id',
        'fitness_class_id',
        'booking_date',
        'status',
        'amount_paid',
        'payment_method',
        'notes',
        'attendance_status',
        'checked_in_at',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'checked_in_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    // Status constants
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';
    const STATUS_WAITLIST = 'waitlist';

    // Attendance status constants
    const ATTENDANCE_ATTENDED = 'attended';
    const ATTENDANCE_NO_SHOW = 'no_show';
    const ATTENDANCE_LATE = 'late';
    const ATTENDANCE_PENDING = 'pending';

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

    public function fitnessClass()
    {
        return $this->belongsTo(FitnessClass::class);
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if member attended the class
     */
    public function wasAttended()
    {
        return $this->attendance_status === self::ATTENDANCE_ATTENDED;
    }

    /**
     * Mark as checked in
     */
    public function checkIn()
    {
        $this->update([
            'attendance_status' => self::ATTENDANCE_ATTENDED,
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Scopes
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeAttended($query)
    {
        return $query->where('attendance_status', self::ATTENDANCE_ATTENDED);
    }

    public function scopeNoShow($query)
    {
        return $query->where('attendance_status', self::ATTENDANCE_NO_SHOW);
    }

    public function scopeForToday($query)
    {
        return $query->whereHas('fitnessClass', function ($q) {
            $q->whereDate('class_date', today());
        });
    }
}