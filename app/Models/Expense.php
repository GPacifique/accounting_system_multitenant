<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class Expense extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'date',
        'category',
        'description',
        'project_id',
        'client_id',
        'amount',
        'method',
        'status',
        'user_id',
    ];

    // Optional: centralised categories
    public const CATEGORIES = [
        'Materials',
        'Labor',
        'Equipment',
        'Subcontractor',
        'Transport',
        'Utilities',
        'Permits',
        'Miscellaneous',
    ];

    /**
     * Expense belongs to a Project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

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
     * Expense belongs to a Tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
