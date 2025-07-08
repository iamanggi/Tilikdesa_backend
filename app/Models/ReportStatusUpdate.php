<?php
// app/Models/ReportStatusUpdate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportStatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'updated_by',
        'previous_status',
        'new_status',
        'notes',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}