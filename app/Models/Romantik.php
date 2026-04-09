<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Romantik extends Model
{
    use HasFactory;
    protected $table = 'romantik';
    protected $fillable = [
        'judul_kegiatan', 'tahun_kegiatan', 'nomor_rekomendasi', 'tgl_pengajuan', 'tgl_perbaikan_terakhir',
        'tgl_selesai', 'lama_pemeriksaan','status_pengajuan','status_rekomendasi','opd_id'
    ];  
    public function opd() {
        return $this->belongsTo(Opd::class, 'opd_id');
    }
}   
