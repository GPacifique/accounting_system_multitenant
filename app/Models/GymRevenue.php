<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\BelongsToTenant;

class GymRevenue extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'gym_revenues';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'member_id',
        'membership_id',
        'trainer_id',
        'fitness_class_id',
        'revenue_type',
        'description',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_date',
        'receipt_number',
        'notes',
        'processed_by',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Revenue type constants
    const TYPE_MEMBERSHIP = 'membership';
    const TYPE_PERSONAL_TRAINING = 'personal_training';
    const TYPE_CLASS_BOOKING = 'class_booking';
    const TYPE_DAY_PASS = 'day_pass';
    const TYPE_PRODUCT_SALE = 'product_sale';
    const TYPE_LOCKER_RENTAL = 'locker_rental';
    const TYPE_LATE_FEE = 'late_fee';
    const TYPE_REGISTRATION_FEE = 'registration_fee';
    const TYPE_OTHER = 'other';

    // Payment method constants
    const PAYMENT_CASH = 'cash';
    const PAYMENT_CARD = 'card';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_DIRECT_DEBIT = 'direct_debit';
    const PAYMENT_MOBILE_PAYMENT = 'mobile_payment';
    const PAYMENT_CHECK = 'check';

    // Payment status constants
    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_DISPUTED = 'disputed';

    /**
     * Automatically set receipt number on create if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (GymRevenue $revenue) {
            if (empty($revenue->receipt_number)) {
                $revenue->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    /**
     * Generate a unique receipt number.
     * Format: RCP-YYYYMMDD-XXXX
     */
    public static function generateReceiptNumber(int $suffixLength = 4): string
    {
        $date = Carbon::now()->format('Ymd');
        do {
            $suffix = strtoupper(Str::random($suffixLength));
            $candidate = "RCP-{$date}-{$suffix}";
        } while (static::where('receipt_number', $candidate)->exists());

        return $candidate;
    }

    /**
     * Relationships
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function fitnessClass(): BelongsTo
    {
        return $this->belongsTo(FitnessClass::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === self::STATUS_PENDING;
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->payment_status === self::STATUS_FAILED;
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('revenue_type', $type);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeForPeriod($query, $startDate, $endDate = null)
    {
        $query->where('transaction_date', '>=', $startDate);
        
        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }
        
        return $query;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('transaction_date', now()->year)
                    ->whereMonth('transaction_date', now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('transaction_date', now()->year);
    }

    /**
     * Static methods for reporting
     */
    public static function getTotalRevenueForPeriod($tenantId, $startDate, $endDate = null)
    {
        $query = static::where('tenant_id', $tenantId)
                      ->completed()
                      ->where('transaction_date', '>=', $startDate);

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $query->sum('amount');
    }

    public static function getRevenueByType($tenantId, $startDate = null, $endDate = null)
    {
        $query = static::where('tenant_id', $tenantId)->completed();

        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $query->selectRaw('revenue_type, SUM(amount) as total')
                    ->groupBy('revenue_type')
                    ->pluck('total', 'revenue_type');
    }

    public static function getTopMembers($tenantId, $limit = 10, $startDate = null, $endDate = null)
    {
        $query = static::where('tenant_id', $tenantId)
                      ->completed()
                      ->whereNotNull('member_id');

        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        return $query->selectRaw('member_id, SUM(amount) as total_spent')
                    ->groupBy('member_id')
                    ->orderByDesc('total_spent')
                    ->limit($limit)
                    ->with('member')
                    ->get();
    }
}