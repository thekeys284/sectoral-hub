<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\EventRegistration;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'opd_id',
        'profile_photo_path',
        'no_hp',
        'tim',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tim' => 'array',
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

    public function opdBinaan()
    {
        return $this->hasMany(Opd::class, 'pembina_id', 'id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'user_id');
    }

    /**
     * Relasi Many-to-Many ke Model Event yang diikuti oleh User
     */
    public function registeredEvents()
    {
        // Parameter: Model Tujuan, Nama Tabel Pivot, Foreign Key User, Foreign Key Event
        return $this->belongsToMany(Event::class, 'event_registrations')
                ->withTimestamps();
    }
}
