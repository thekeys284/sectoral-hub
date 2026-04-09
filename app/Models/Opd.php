<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Opd extends Model
{
    use HasFactory;
    protected $table = 'opd';
    protected $fillable = [
        'name',
        'alamat',
        'logo_path',
        'email',
        'no_kantor',
        'pembina_id',
    ];

    public function users():HasMany{
        return $this->hasMany(User::class, 'opd_id');
    }
    public function kegiatan():HasMany{
        return $this->hasMany(Kegiatan::class, 'opd_id');
    }
    public function metadata():HasMany{
        return $this->hasMany(Metadata::class, 'opd_id');
    }
    public function romantik():HasMany{
        return $this->hasMany(Romantik::class, 'opd_id');
    }   
    public function daftardata():HasMany{
        return $this->hasMany(DaftarData::class, 'opd_id');
    }
    public function pembina(): BelongsTo {
        return $this->belongsTo(User::class, 'pembina_id');
    }
    
}
