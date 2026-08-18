<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetadataSubmission extends Model
{
    use HasFactory;

    protected $table = 'metadata_submission';

    protected $fillable = [
        'kegiatan_id',
        'tipe',
        'link_url',
        'status',
        'catatan_pembina',
        'reviewed_by',
        'reviewed_at',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}