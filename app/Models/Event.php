<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $table = 'event';
    protected $fillable = [
        'title','start_at','end_at','lokasi_event','deskripsi','category',
        'meeting_link','link_materi','daftar_hadir','pretest','posttest','evaluasi',
        'sertifikat','is_active','image_banner','virtual_bg','created_by'
    ];

    public function creator():BelongsTo{
        return $this->belongsTo(User::Class, 'created_by');
    }

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
