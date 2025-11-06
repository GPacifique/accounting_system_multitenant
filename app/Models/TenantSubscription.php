<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class TenantSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan',
        'status',
        'amount',
        'currency',
        'billing_cycle',
        'current_period_start',
        'current_period_end',
        'trial_start',
        'trial_end',
        'cancelled_at',
        'paused_at',
        'features',
        'usage_limits',
        'usage_current',
        'external_subscription_id',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_start' => 'datetime',
        'trial_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'paused_at' => 'datetime',
        'features' => 'array',
        'usage_limits' => 'array',
        'usage_current' => 'array',
        'amount' => 'decimal:2',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_PAUSED = 'paused';

    const PLAN_BASIC = 'basic';
    const PLAN_PROFESSIONAL = 'professional';
    const PLAN_ENTERPRISE = 'enterprise';

    const BILLING_MONTHLY = 'monthly';
    const BILLING_YEARLY = 'yearly';

    const PLANS_CONFIG = [
        'basic' => [
            'name' => 'Basic',
            'monthly_price' => 29.99,
            'yearly_price' => 299.99,
            'features' => [
                'max_users' => 5,
                'max_projects' => 10,
                'storage_gb' => 5,
                'support' => 'email',
                'analytics' => false,
                'api_access' => false,
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'monthly_price' => 99.99,
            'yearly_price' => 999.99,
            'features' => [
                'max_users' => 25,
                'max_projects' => 100,
                'storage_gb' => 50,
                'support' => 'priority',
                'analytics' => true,
                'api_access' => true,
                'custom_branding' => true,
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'monthly_price' => 299.99,
            'yearly_price' => 2999.99,
            'features' => [
                'max_users' => -1, // unlimited
                'max_projects' => -1, // unlimited
                'storage_gb' => 500,
                'support' => 'dedicated',
                'analytics' => true,
                'api_access' => true,
                'custom_branding' => true,
                'white_label' => true,
                'sso' => true,
            ],
        ],
    ];

    /**
     * Get the tenant that owns this subscription.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->current_period_end > now();
    }

    /**
     * Check if subscription is in trial period.
     */
    public function isInTrial(): bool
    {
        return $this->trial_end && $this->trial_end > now();
    }

    /**
     * Check if subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->status === self::STATUS_PAST_DUE || 
               ($this->status === self::STATUS_ACTIVE && $this->current_period_end < now());
    }

    /**
     * Get plan configuration.
     */
    public function getPlanConfig(): array
    {
        return self::PLANS_CONFIG[$this->plan] ?? [];
    }

    /**
     * Get plan name.
     */
    public function getPlanName(): string
    {
        return $this->getPlanConfig()['name'] ?? ucfirst($this->plan);
    }

    /**
     * Get plan price for current billing cycle.
     */
    public function getPlanPrice(): float
    {
        $config = $this->getPlanConfig();
        $priceKey = $this->billing_cycle === self::BILLING_YEARLY ? 'yearly_price' : 'monthly_price';
        return $config[$priceKey] ?? 0;
    }

    /**
     * Check if plan has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        $planFeatures = $this->getPlanConfig()['features'] ?? [];
        $subscriptionFeatures = $this->features ?? [];
        
        // Check subscription-specific features first, then plan defaults
        return $subscriptionFeatures[$feature] ?? $planFeatures[$feature] ?? false;
    }

    /**
     * Get feature limit.
     */
    public function getFeatureLimit(string $feature): int
    {
        $planFeatures = $this->getPlanConfig()['features'] ?? [];
        $subscriptionLimits = $this->usage_limits ?? [];
        
        return $subscriptionLimits[$feature] ?? $planFeatures[$feature] ?? 0;
    }

    /**
     * Get current usage for a feature.
     */
    public function getCurrentUsage(string $feature): int
    {
        return $this->usage_current[$feature] ?? 0;
    }

    /**
     * Check if usage limit is exceeded.
     */
    public function isUsageLimitExceeded(string $feature): bool
    {
        $limit = $this->getFeatureLimit($feature);
        $current = $this->getCurrentUsage($feature);
        
        return $limit > 0 && $current >= $limit; // -1 means unlimited
    }

    /**
     * Update usage for a feature.
     */
    public function updateUsage(string $feature, int $value): void
    {
        $currentUsage = $this->usage_current ?? [];
        $currentUsage[$feature] = $value;
        $this->update(['usage_current' => $currentUsage]);
    }

    /**
     * Increment usage for a feature.
     */
    public function incrementUsage(string $feature, int $increment = 1): void
    {
        $current = $this->getCurrentUsage($feature);
        $this->updateUsage($feature, $current + $increment);
    }

    /**
     * Cancel the subscription.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Pause the subscription.
     */
    public function pause(): void
    {
        $this->update([
            'status' => self::STATUS_PAUSED,
            'paused_at' => now(),
        ]);
    }

    /**
     * Resume the subscription.
     */
    public function resume(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'paused_at' => null,
        ]);
    }

    /**
     * Renew the subscription for next billing cycle.
     */
    public function renew(): void
    {
        $periodLength = $this->billing_cycle === self::BILLING_YEARLY ? 12 : 1;
        
        $this->update([
            'current_period_start' => $this->current_period_end,
            'current_period_end' => $this->current_period_end->addMonths($periodLength),
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Get days until subscription expires.
     */
    public function getDaysUntilExpiry(): int
    {
        return max(0, now()->diffInDays($this->current_period_end, false));
    }

    /**
     * Get status badge color class.
     */
    public function getStatusColorClass(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            self::STATUS_PAST_DUE => 'bg-orange-100 text-orange-800',
            self::STATUS_PAUSED => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('current_period_end', '<', now());
    }

    /**
     * Scope for expiring soon.
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('current_period_end', '<=', now()->addDays($days))
                    ->where('current_period_end', '>', now());
    }
}