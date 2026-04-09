<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metadata extends Model
{
    use HasFactory;
    protected $table = 'metadata';
    protected $fillable = [
        'judul_kegiatan', 'periode_submission','tanggal_submission','status',
        'opd_id'
    ];  

    public function opd() {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

}
