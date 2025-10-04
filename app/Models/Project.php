<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'start_date',
        'status',      // e.g., planned, active, completed
        'start_date',
        'end_date',
        'contract_value',
        'amount_paid',
        'amount_remaining',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
    ];
public function client()
    {
        return $this->belongsTo(Client::class);
    }   
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

}
