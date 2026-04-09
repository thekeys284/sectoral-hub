<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kegiatan extends Model
{
    use HasFactory;
    protected $table = 'kegiatan';
    protected $fillable = [
        'nama_kegiatan', 'periode_kegiatan', 'tahun_kegiatan', 'cara_pengumpulan_data',
        'data_utama', 'data_prioritas', 'aksesbilitas', 'opd_id', 'deskripsi',
        'metadata_id','romantik_id',
    ];

    public function opd() : BelongsTo{
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function metadata() : BelongsTo{
        return $this->belongsTo(Metadata::class, 'metadata_id');
    }       

    public function romantik() : BelongsTo{
        return $this->belongsTo(Romantik::class, 'romantik_id');
    }
}
