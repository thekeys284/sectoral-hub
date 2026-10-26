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
        'nip', // Tambahkan baris ini
        'referral_code', // Tambahkan baris ini
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'role' => '["operator"]', 
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tim' => 'array',
        'role' => 'array',
    ];
    public function isAdmin(): bool
    {
        return is_array($this->role) && in_array('admin', $this->role);
    }

    public function hasRole(string $roleName): bool
    {
        return is_array($this->role) && in_array($roleName, $this->role);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path 
            ? asset('storage/' . $this->profile_photo_path) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    protected ?Opd $cachedOriginOpd = null;
    protected bool $originOpdLoaded = false;

    /**
     * Mengambil OPD asal (satker asli user di tabel users) tanpa resolusi parent_id
     */
    public function getOriginOpdAttribute(): ?Opd
    {
        $rawOpdId = $this->attributes['opd_id'] ?? null;
        if (!$rawOpdId) {
            return null;
        }
        if (!$this->originOpdLoaded) {
            $this->cachedOriginOpd = Opd::with('parent')->find($rawOpdId);
            $this->originOpdLoaded = true;
        }
        return $this->cachedOriginOpd;
    }

    /**
     * Mengambil role yang sedang aktif pada session
     */
    public function getActiveRoleAttribute(): string
    {
        $rawRole = $this->role;
        $userRoles = is_string($rawRole)
            ? (json_decode($rawRole, true) ?? [$rawRole])
            : (is_array($rawRole) ? $rawRole : []);
        $sessionRole = session('active_role');
        if (!empty($sessionRole) && in_array($sessionRole, $userRoles)) {
            return $sessionRole;
        }
        return $userRoles[0] ?? '';
    }

    /**
     * Resolusi otomatis opd_id:
     * - Jika user yang login sedang berada di mode 'produsen' (Akun Instansi) dan
     *   OPD asalnya memiliki parent_id (misal Rumah Sakit di bawah OPD Induk),
     *   maka opd_id otomatis mengarah ke parent_id.
     * - Jika OPD tidak punya induk (parent_id = null) atau user sedang di mode 'operator' (Akun Pribadi),
     *   tetap mengembalikan opd_id asli.
     */
    public function getOpdIdAttribute($value)
    {
        if (!$value) {
            return $value;
        }

        if (auth()->check() && auth()->id() === ($this->attributes['id'] ?? null)) {
            if ($this->active_role === 'produsen') {
                $originOpd = $this->origin_opd;
                if ($originOpd && $originOpd->parent_id) {
                    return $originOpd->parent_id;
                }
            }
        }

        return $value;
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
