<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'invoice_number',
        'amount_received',
        'payment_status',
        'amount_remaining',
        'received_at',
        'notes',
    ];

    // Cast received_at to Carbon date
    protected $casts = [
        'received_at' => 'date', // or 'datetime' if you want time included
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
