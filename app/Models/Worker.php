<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Worker extends Model
{
use HasFactory, SoftDeletes;

public function index()
{
    $workers = Worker::orderBy('last_name')->paginate(15);

    // total across DB
    $totalWorkers = Worker::count();

    return view('workers.index', compact('workers', 'totalWorkers'));
}


protected $table = 'workers';


protected $fillable = [
'first_name',
'last_name',
'email',
'phone',
'position',
'salary_cents',
'currency',
'hired_at',
'status',
'notes',
];


protected $casts = [
'salary_cents' => 'integer',
'hired_at' => 'datetime',
];


// Accessor to get salary as decimal
public function getSalaryAttribute()
{
return $this->salary_cents !== null ? ($this->salary_cents / 100) : null;
}


public function setSalaryAttribute($value)
{
// Accept either decimal (e.g. 1234.56) or integer cents
$this->attributes['salary_cents'] = is_numeric($value) ? (int) round($value * 100) : null;
}


public function getFullNameAttribute()
{
return trim("{$this->first_name} {$this->last_name}");
}
}