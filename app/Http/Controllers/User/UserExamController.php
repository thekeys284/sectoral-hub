<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\EventRegistration; // Model untuk menyimpan skor/hasil ujian
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserExamController extends Controller
{
    /**
     * Halaman pengerjaan Ujian (Pretest / Posttest)
     */
    public function show(Request $request, $eventId, $type)
    {
        // $type = 'pretest' atau 'posttest'
        if (!in_array($type, ['pretest', 'posttest'])) {
            abort(404);
        }

        $event = Event::findOrFail($eventId);
        $now = \Carbon\Carbon::now('Asia/Jakarta');

        // Cek apakah waktu event sudah berakhir untuk ujian
        if ($event->end_at && $now->gt($event->end_at)) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', "Waktu pengerjaan {$type} telah berakhir.");
        }

        // Jika Posttest dan memiliki password, pastikan password sudah diverifikasi
        if ($type === 'posttest' && $event->posttest_password) {
            if (!session("posttest_verified_{$event->id}")) {
                return redirect()->route('user.events.show', $event->id)
                    ->with('error', 'Silakan masukkan password posttest terlebih dahulu.');
            }
        }

        // Ambil soal berdasarkan tipe ujian
        $questions = EventQuestion::where('event_id', $event->id)
            ->where('type', $type)
            ->get();

        return view('user.exams.show', compact('event', 'questions', 'type'));
    }

    /**
     * Verifikasi Password Posttest
     */
    public function verifyPassword(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $request->validate([
            'password' => 'required|string',
        ]);

        if ($request->password === $event->posttest_password) {
            // Simpan flag di session bahwa password sudah benar
            session(["posttest_verified_{$event->id}" => true]);

            return redirect()->route('user.exams.show', [$event->id, 'posttest']);
        }

        return back()->with('error', 'Password Posttest salah!');
    }

    /**
     * Submit Jawaban Ujian & Penilaian
     */
    // public function submit(Request $request, $eventId, $type)
    // {
    //     $event = Event::findOrFail($eventId);
    //     $questions = EventQuestion::where('event_id', $event->id)
    //         ->where('type', $type)
    //         ->get();

    //     $answers = $request->input('answers', []); // ['question_id' => 'option_key']
    //     $correctAnswersCount = 0;
    //     $totalQuestions = $questions->count();

    //     foreach ($questions as $q) {
    //         $userAnswer = $answers[$q->id] ?? null;
    //         if ($userAnswer && strtolower($userAnswer) === strtolower($q->correct_answer)) {
    //             $correctAnswersCount++;
    //         }
    //     }

    //     // Hitung Skor (Skala 0 - 100)
    //     $score = $totalQuestions > 0 ? round(($correctAnswersCount / $totalQuestions) * 100, 2) : 0;
    //     $isPassed = $score >= $event->passing_grade;

    //     // Simpan Hasil Ujian
    //     UserExamResult::updateOrCreate([
    //         'user_id'  => Auth::id(),
    //         'event_id' => $event->id,
    //         'type'     => $type,
    //     ], [
    //         'score'     => $score,
    //         'is_passed' => $isPassed,
    //         'answers'   => $answers, // tersimpan otomatis sebagai JSON
    //     ]);

    //     return redirect()->route('user.events.show', $event->id)
    //         ->with('success', "Ujian {$type} selesai! Nilai Anda: {$score}");
    // }

    // 1. Halaman Persiapan / Konfirmasi
    public function confirm($eventId, $type)
    {
        $event = Event::findOrFail($eventId);
        $questionsCount = EventQuestion::where('event_id', $event->id)->where('type', $type)->count();

        return view('user.exams.confirm', compact('event', 'type', 'questionsCount'));
    }

// 2. Submit Jawaban & Redirect ke Hasil Nilai
    public function submit(Request $request, $eventId, $type)
    {
        $event = Event::findOrFail($eventId);
        $questions = EventQuestion::where('event_id', $event->id)->where('type', $type)->get();

        $now = \Carbon\Carbon::now('Asia/Jakarta');

        // Cek apakah waktu event sudah berakhir untuk submit ujian
        if ($event->end_at && $now->gt($event->end_at)) {
            return redirect()->route('user.events.show', $event->id)
                ->with('error', "Waktu pengiriman jawaban {$type} telah berakhir.");
        }
        // 1. Ambil pendaftaran peserta
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 2. Ambil jawaban yang diinput peserta
        $answers = $request->input('answers', []); // Format: [question_id => 'a', question_id => 'c', ...]
        $correctAnswersCount = 0;
        $totalQuestions = $questions->count();

        // 3. Hitung Jawaban Benar
        foreach ($questions as $q) {
            $userAnswer = $answers[$q->id] ?? null;
            if ($userAnswer && strtolower($userAnswer) === strtolower($q->correct_answer)) {
                $correctAnswersCount++;
            }
        }

        // 4. Hitung Skor
        $score = $totalQuestions > 0 ? round(($correctAnswersCount / $totalQuestions) * 100) : 0;
        $isPassed = $score >= $event->passing_grade;

        // 5. Simpan Skor & Detail Jawaban ke Database
        if ($type === 'pretest') {
            $registration->update([
                'score_pretest'   => $score,
                'answers_pretest' => $answers, // Otomatis tersimpan sebagai JSON
            ]);
        } else {
            $registration->update([
                'score_posttest'   => $score,
                'answers_posttest' => $answers, // Otomatis tersimpan sebagai JSON
            ]);
        }

        return view('user.exams.result', compact('event', 'type', 'score', 'isPassed'));
    }    
}