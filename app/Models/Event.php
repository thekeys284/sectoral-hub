<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';
    protected $fillable = ['title','start_at','end_at','lokasi_event','deskripsi','category',
                            'meeting_link','link_materi','certificate_link','image_banner','virtual_bg',
                            'absensi_start','absensi_end','passing_grade','posttest_password',
                            'is_active','created_by'];

    protected $casts=[
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'absensi_start' => 'datetime',
        'absensi_end' => 'datetime',
        'is_active' => 'boolean',
        'passing_grade' => 'integer'
    ];

    public function creator():BelongsTo{
        return $this->belongsTo(User::Class, 'created_by');
    }

    public function questions():HasMany{
        return $this->hasMany(EventQuestion::class, 'event_id');
    }

    public function evaluations():HasMany{
        return $this->hasMany(EventEvaluation::class, 'event_id');
    }

    public function registrations():HasMany{
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function isAbsensiOpen(): bool{
        if(!$this->absensi_start || !$this->absensi_end){
            return false;
        }

        return now()->between($this->absensi_start, $this->absensi_end);
    }
}
