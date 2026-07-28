<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRegistration extends Model
{
    use HasFactory;

    protected $table = 'event_registrations';

    protected $fillable = [
        'event_id',
        'user_id',
        'status_kehadiran',
        'score_pretest',
        'score_posttest',
        'answers_pretest',  // Menyimpan lembar jawaban JSON pretest
        'answers_posttest', // Menyimpan lembar jawaban JSON posttest
        'sertifikat_path'
    ];

    protected $casts = [
        'status_kehadiran' => 'boolean',
        'score_pretest'    => 'integer',
        'score_posttest'   => 'integer',
        'answers_pretest'  => 'array',
        'answers_posttest' => 'array',
    ];

    /**
     * Relasi ke Model Event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    
    /**
     * Relasi ke Model User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Jawaban Evaluasi Peserta
     */
    public function evaluationAnswer(): HasMany
    {
        return $this->hasMany(EventEvaluationAnswer::class, 'registration_id');
    }

    /**
     * Accessor untuk menghitung Nilai Akhir Komposit
     */
    public function getNilaiAkhirAttribute(): float
    {
        // 1. Kehadiran (Bobot 20%)
        $nilaiAbsen = $this->status_kehadiran ? 100 : 0;
        $bobotAbsen = $nilaiAbsen * 0.20;

        // 2. Pretest (Bobot 25%)
        $nilaiPretest = $this->score_pretest ?? 0;
        $bobotPretest = $nilaiPretest * 0.25;

        // 3. Posttest (Bobot 50%)
        $nilaiPosttest = $this->score_posttest ?? 0;
        $bobotPosttest = $nilaiPosttest * 0.50;

        // 4. Evaluasi (Bobot 5%)
        // Pengecekan disesuaikan langsung menggunakan relasi registration_id milik instance ini
        $sudahEvaluasi = EventEvaluationAnswer::where('registration_id', $this->id)->exists();
        $nilaiEvaluasi = $sudahEvaluasi ? 100 : 0;
        $bobotEvaluasi = $nilaiEvaluasi * 0.05;

        // Total Nilai Akhir
        return round($bobotAbsen + $bobotEvaluasi + $bobotPosttest + $bobotPretest, 2);
    }

    /**
     * Cek apakah peserta LULUS pelatihan
     */
    public function isLulus(): bool
    {
        $passingGrade = $this->event->passing_grade ?? 70;
        return $this->nilai_akhir >= $passingGrade;
    }
}