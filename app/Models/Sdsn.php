<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Sdsn extends Model
{
    use HasFactory;
    protected $table = 'Sdsn';
    protected $fillable = [
        'kode_sdsn', 'nama_data', 'konsep', 'definisi', 'klasifikasi_penyajian',
        'ukuran', 'satuan', 'tahun_penetapan'
    ];

    public function sdsn():HasMany{
        return $this->hasMany(DaftarData::class, 'kode_sdsn');
    }
}
