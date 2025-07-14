<?php
// app/Models/Report.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'category_id',
        'title',
        'description',
        'latitude',
        'longitude',
        'address',
        'photos',
        'status',
        'admin_notes',
        'verified_at',
        'processed_at',
        'completed_at',
        'is_verified',
    ];

    protected $casts = [
        'photos' => 'array',
        'verified_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function statusUpdates()
    {
        return $this->hasMany(ReportStatusUpdate::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Boot method for auto-generating report number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
        });
    }

    // Helper methods
    public function canBeDeleted()
    {
        return in_array($this->status, ['baru', 'selesai']);
    }

    public function canBeRated()
    {
        return $this->status === 'selesai';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'baru' => 'Baru',
            'diverifikasi' => 'Diverifikasi',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
}