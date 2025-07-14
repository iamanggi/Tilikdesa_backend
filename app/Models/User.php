<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $primaryKey = 'id_user';
    public $incrementing = true; // ← Tambahkan ini
    protected $keyType = 'int';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'username',
        'role',
        'bahasa',
        'photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMasyarakat()
    {
        return $this->role === 'masyarakat';
    }

    public function getAuthIdentifierName()
{
    return 'id_user';
}

}
