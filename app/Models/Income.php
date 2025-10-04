<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'project_id',
        'invoice_number',
        'amount_received',
        'payment_status',
        'amount_remaining',
        'received_at',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'received_at' => 'date', // Use 'datetime' if you want time included
        'amount_received' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
    ];

    /**
     * Predefined payment statuses for validation or dropdowns.
     */
    public const PAYMENT_STATUSES = [
        'Pending',
        'Partial',
        'Paid',
        'Overdue',
    ];
 

    /**
     * Expense belongs to a Client (vendor / supplier / worker).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * Expense belongs to a User (registered by).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }


    /**
     * Income belongs to a Project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Check if income is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'Paid' || $this->amount_remaining <= 0;
    }
}
