<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'tenant_id',
        'member_id',
        'checked_in_at',
        'checked_out_at',
        'notes',
        'created_by',
    ];

    protected $dates = [
        'checked_in_at',
        'checked_out_at',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
