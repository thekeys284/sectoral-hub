<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        $event = $this->event;
        $hasPretest  = $event ? ($event->has_pretest ?? true) : true;
        $hasPosttest = $event ? ($event->has_posttest ?? true) : true;
        $hasEvaluasi = $event ? ($event->has_evaluasi ?? true) : true;

        // 1. Kehadiran (Bobot 20%)
        $nilaiAbsen = $this->status_kehadiran ? 100 : 0;
        $bobotAbsen = $nilaiAbsen * 0.20;
        $totalBobot = 0.20;

        // 2. Pretest (Bobot 25%)
        $bobotPretest = 0;
        if ($hasPretest) {
            $nilaiPretest = $this->score_pretest ?? 0;
            $bobotPretest = $nilaiPretest * 0.25;
            $totalBobot += 0.25;
        }

        // 3. Posttest (Bobot 50%)
        $bobotPosttest = 0;
        if ($hasPosttest) {
            $nilaiPosttest = $this->score_posttest ?? 0;
            $bobotPosttest = $nilaiPosttest * 0.50;
            $totalBobot += 0.50;
        }

        // 4. Evaluasi (Bobot 5%)
        $bobotEvaluasi = 0;
        if ($hasEvaluasi) {
            $sudahEvaluasi = EventEvaluationAnswer::where('registration_id', $this->id)->exists();
            $nilaiEvaluasi = $sudahEvaluasi ? 100 : 0;
            $bobotEvaluasi = $nilaiEvaluasi * 0.05;
            $totalBobot += 0.05;
        }

        // Total Nilai Akhir (dinormalisasi ke skala 100 jika ada komponen yang dinonaktifkan)
        $rawTotal = $bobotAbsen + $bobotEvaluasi + $bobotPosttest + $bobotPretest;
        return $totalBobot > 0 ? round($rawTotal / $totalBobot, 2) : 0;
    }

    /**
     * Cek apakah peserta LULUS pelatihan
     */
    public function isLulus(): bool
    {
        $passingGrade = $this->event->passing_grade ?? 70;
        return $this->nilai_akhir >= $passingGrade;
    }

    /**
     * Accessor untuk mendapatkan link / file sertifikat peserta
     */
    public function getCertificateUrlAttribute(): ?string
    {
        // 1. Jika event diatur 'Satu link untuk semua peserta'
        if ($this->event && $this->event->certificate_type === 'all') {
            return $this->event->certificate_link ?: $this->sertifikat_path;
        }

        // 2. Jika unik per peserta (atau sertifikat individual)
        if ($this->sertifikat_path) {
            if (Str::startsWith($this->sertifikat_path, ['http://', 'https://'])) {
                return $this->sertifikat_path;
            }
            return asset('storage/' . $this->sertifikat_path);
        }

        return null;
    }
}