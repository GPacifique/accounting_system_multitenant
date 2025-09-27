<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',          // e.g. "Payroll Report"
        'type',          // e.g. "payroll", "expenses", "financial"
        'description',   // optional text describing what this report does
        'filters',       // JSON of filters used when generating report
        'generated_at',  // timestamp when it was last generated
        'file_path',     // path to stored PDF/CSV/Excel
    ];

    protected $casts = [
        'filters' => 'array',
        'generated_at' => 'datetime',
    ];
}
