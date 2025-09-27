<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'method',
        'reference',
    ];

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
