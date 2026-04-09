<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'opd_id',
        'profile_photo_path',
        'no_hp'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path 
            ? asset('storage/' . $this->profile_photo_path) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    public function opd(){
        return $this->belongsTo(Opd::class, 'opd_id');
    }
}
