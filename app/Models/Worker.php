<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'role',
    ];

    /**
     * A worker can have many tasks.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the total wages for this worker.
     */
    public function totalWages()
    {
        return $this->tasks()->sum('daily_wage');
    }

    /**
     * Get wages for a specific date.
     */
    public function wagesByDate($date)
    {
        return $this->tasks()
                    ->where('date', $date)
                    ->sum('daily_wage');
    }
}
