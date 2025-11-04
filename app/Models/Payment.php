<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'method',
        'reference',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Employee relationship
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Example: if payments belong to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Example: if payments belong to an order/invoice
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
