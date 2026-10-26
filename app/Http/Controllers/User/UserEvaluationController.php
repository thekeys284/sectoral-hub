<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventEvaluation;
use App\Models\EventEvaluationAnswer;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEvaluationController extends Controller
{
    /**
     * Halaman Kuesioner Evaluasi
     */
    public function create($eventId)
    {
        $event = Event::findOrFail($eventId);

        // 1. Ambil pendaftaran user untuk event ini
        $registration = EventRegistration::where('event_id', $eventId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 2. Cek apakah peserta sudah pernah mengisi evaluasi untuk pendaftaran ini
        $hasFilled = EventEvaluationAnswer::where('registration_id', $registration->id)->exists();

        if ($hasFilled) {
            return redirect()->route('user.events.show', $event->id)
                ->with('info', 'Anda sudah mengisi evaluasi untuk event ini.');
        }

        if (!($event->has_evaluasi ?? true)) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', 'Kuesioner evaluasi tidak diaktifkan untuk kegiatan ini.');
        }

        // 3. VALIDASI: Pastikan Pretest dan Posttest yang diaktifkan SUDAH dikerjakan
        if (($event->has_pretest ?? true) && $registration->score_pretest === null) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', 'Silakan selesaikan Ujian Pretest terlebih dahulu sebelum mengisi evaluasi.');
        }

        if (($event->has_posttest ?? true) && $registration->score_posttest === null) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', 'Silakan selesaikan Ujian Posttest terlebih dahulu sebelum mengisi evaluasi.');
        }

        // 4. Ambil pertanyaan evaluasi yang aktif untuk event ini (termasuk master/khusus)
        $evaluations = EventEvaluation::where(function ($query) use ($eventId) {
            $query->where('event_id', $eventId)
                  ->orWhere('is_master', true);
        })->where('is_active', true)->get();

        return view('user.evaluations.create', compact('event', 'evaluations', 'registration'));
    }

    /**
     * Simpan Jawaban Evaluasi Peserta
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // Ambil data pendaftaran peserta
        $registration = EventRegistration::where('event_id', $eventId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Cek kembali agar tidak double submit
        $hasFilled = EventEvaluationAnswer::where('registration_id', $registration->id)->exists();
        if ($hasFilled) {
            return redirect()->route('user.events.show', $event->id)
                ->with('info', 'Anda sudah mengisi evaluasi untuk event ini.');
        }

        // VALIDASI: Cek kembali Pretest dan Posttest yang aktif sebelum menyimpan
        $needPretest = ($event->has_pretest ?? true) && $registration->score_pretest === null;
        $needPosttest = ($event->has_posttest ?? true) && $registration->score_posttest === null;
        if ($needPretest || $needPosttest) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', 'Evaluasi hanya dapat diisi setelah menyelesaikan ujian yang diwajibkan.');
        }

        $answers = $request->input('answers', []); // Format: ['evaluation_id' => 'nilai_atau_teks']

        foreach ($answers as $evaluationId => $answerValue) {
            $evalQuestion = EventEvaluation::find($evaluationId);

            if ($evalQuestion) {
                EventEvaluationAnswer::create([
                    'registration_id' => $registration->id,
                    'evaluation_id'   => $evaluationId,
                    'rating'          => $evalQuestion->type === 'scale' ? (int) $answerValue : null,
                    'answer_text'     => $evalQuestion->type === 'text' ? $answerValue : null,
                ]);
            }
        }

        return redirect()->route('user.events.show', $event->id)
            ->with('success', 'Terima kasih telah mengisi evaluasi kegiatan!');
    }
}