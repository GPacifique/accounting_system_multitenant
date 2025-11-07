<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Trainer extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'specializations',
        'certifications',
        'hire_date',
        'hourly_rate',
        'commission_rate',
        'status',
        'bio',
        'experience_years',
        'languages_spoken',
        'availability_schedule',
        'profile_image',
    ];

    protected $casts = [
        'specializations' => 'array',
        'certifications' => 'array',
        'languages_spoken' => 'array',
        'availability_schedule' => 'array',
        'hire_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ON_LEAVE = 'on_leave';
    const STATUS_TERMINATED = 'terminated';

    // Specialization constants
    const SPEC_PERSONAL_TRAINING = 'personal_training';
    const SPEC_GROUP_FITNESS = 'group_fitness';
    const SPEC_YOGA = 'yoga';
    const SPEC_PILATES = 'pilates';
    const SPEC_STRENGTH_TRAINING = 'strength_training';
    const SPEC_CARDIO = 'cardio';
    const SPEC_NUTRITION = 'nutrition';
    const SPEC_REHABILITATION = 'rehabilitation';
    const SPEC_SPORTS_SPECIFIC = 'sports_specific';

    /**
     * Get the trainer's full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if trainer is available for booking
     */
    public function isAvailable()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get total earnings for a specific period
     */
    public function getEarnings($startDate = null, $endDate = null)
    {
        $query = $this->personalTrainingSessions()
                      ->where('status', 'completed');

        if ($startDate) {
            $query->where('session_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('session_date', '<=', $endDate);
        }

        return $query->sum('trainer_fee');
    }

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function personalTrainingSessions()
    {
        return $this->hasMany(PersonalTrainingSession::class);
    }

    public function fitnessClasses()
    {
        return $this->hasMany(FitnessClass::class);
    }

    public function payments()
    {
        return $this->hasMany(TrainerPayment::class);
    }

    public function reviews()
    {
        return $this->hasMany(TrainerReview::class);
    }

    /**
     * Get average rating from reviews
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    /**
     * Get total number of completed sessions
     */
    public function getTotalSessionsAttribute()
    {
        return $this->personalTrainingSessions()
                   ->where('status', 'completed')
                   ->count();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeWithSpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeAvailableOnDay($query, $dayOfWeek)
    {
        return $query->whereJsonContains('availability_schedule', $dayOfWeek);
    }

    /**
     * Get upcoming sessions for this trainer
     */
    public function getUpcomingSessions($limit = 10)
    {
        return $this->personalTrainingSessions()
                   ->where('session_date', '>=', now())
                   ->where('status', 'scheduled')
                   ->orderBy('session_date')
                   ->orderBy('start_time')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get trainer's schedule for a specific date
     */
    public function getScheduleForDate($date)
    {
        return $this->personalTrainingSessions()
                   ->whereDate('session_date', $date)
                   ->orderBy('start_time')
                   ->get();
    }
}