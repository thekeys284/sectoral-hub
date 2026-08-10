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

        // 3. VALIDASI BARU: Pastikan Pretest dan Posttest SUDAH dikerjakan
        if ($registration->score_pretest === null) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', 'Silakan selesaikan Ujian Pretest terlebih dahulu sebelum mengisi evaluasi.');
        }

        if ($registration->score_posttest === null) {
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

        // VALIDASI BARU: Cek kembali Pretest dan Posttest sebelum menyimpan
        if ($registration->score_pretest === null || $registration->score_posttest === null) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', 'Evaluasi hanya dapat diisi setelah menyelesaikan Pretest dan Posttest.');
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