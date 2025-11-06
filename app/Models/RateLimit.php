<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RateLimit extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'ip_address',
        'endpoint',
        'method',
        'rate_type',
        'usage_count',
        'limit_count',
        'window_seconds',
        'user_agent',
    ];

    protected $casts = [
        'usage_count' => 'integer',
        'limit_count' => 'integer',
        'window_seconds' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns this rate limit record
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that owns this rate limit record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to recent rate limit records
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', Carbon::now()->subMinutes($minutes));
    }

    /**
     * Scope to specific rate type
     */
    public function scopeOfType($query, string $rateType)
    {
        return $query->where('rate_type', $rateType);
    }

    /**
     * Scope to specific IP address
     */
    public function scopeFromIp($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Check if rate limit was exceeded
     */
    public function isExceeded(): bool
    {
        return $this->usage_count >= $this->limit_count;
    }

    /**
     * Get percentage of limit used
     */
    public function getUsagePercentage(): float
    {
        if ($this->limit_count === 0) {
            return 0;
        }

        return round(($this->usage_count / $this->limit_count) * 100, 2);
    }
}