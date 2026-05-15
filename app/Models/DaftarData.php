<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaftarData extends Model
{
    use HasFactory;
    
    protected $table = 'daftar_data';
    protected $fillable = [
        'nama_data', 'satuan', 'periode','kedalaman_kabkot','sifat_data','sumber_data','opd_id', 'kegiatan_id','aliran_data','nama_aliran_data', 'kode_sdsn'
    ];

    public function opd() : BelongsTo{
        return $this->belongsTo(Opd::class,'opd_id');
    }

    public function kegiatan() : BelongsTo{
        return $this->belongsTo(Kegiatan::class,'kegiatan_id');
    }

    public function sdsn() : BelongsTo{
        return $this->belongsTo(Sdsn::class, 'kode_sdsn');
    }
}
