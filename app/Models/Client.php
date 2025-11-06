<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToTenant;

class Client extends Model

{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}   