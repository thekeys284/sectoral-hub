<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQuestion;
use Illuminate\Http\Request;

class AdminQuestionController extends Controller
{
    /**
     * Menyimpan soal ujian baru (Pretest / Posttest)
     */

    /**
 * Menampilkan daftar bank soal untuk event tertentu
 */
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        $questions = EventQuestion::where('event_id', $eventId)->get();

        return view('admin.questions.index', compact('event', 'questions'));
    }
    public function create($eventId)
    {
        $event = Event::findOrFail($eventId);
        return view('admin.questions.create', compact('event'));
    }
    
    public function store(Request $request, $eventId)
    {
        // 1. Pastikan event-nya ada (Gunakan Model 'Events')
        $event = Event::findOrFail($eventId);

        // 2. Validasi input
        $request->validate([
            'type'           => 'required|array|min:1',
            'type.*'         => 'in:pretest,posttest',
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2',
            'options.a'      => 'required|string',
            'options.b'      => 'required|string',
            'options.c'      => 'nullable|string',
            'options.d'      => 'nullable|string',
            'options.e'      => 'nullable|string',
            'correct_answer' => 'required|in:a,b,c,d,e',
        ], [
            'type.required'  => 'Pilih minimal satu jenis ujian (Pretest atau Posttest).'
        ]);

        // 3. Loop untuk menyimpan soal ke tiap jenis ujian yang dicentang (Pretest/Posttest)
        foreach ($request->type as $jenisUjian) {
            EventQuestion::create([
                'event_id'       => $event->id,
                'type'           => $jenisUjian,
                'question_text'  => $request->question_text,
                'options'        => array_filter($request->options), // Hapus opsi jika ada yang kosong
                'correct_answer' => strtolower($request->correct_answer),
            ]);
        }

        // Format label untuk notifikasi (misal: "PRETEST & POSTTEST")
        $kategoriLabel = strtoupper(implode(' & ', $request->type));

        return redirect()->route('admin.events.show', $event->id)
            ->with('success', "Soal kategori {$kategoriLabel} berhasil ditambahkan!");
    }

    /**
 * Menampilkan form edit soal
 */
    public function edit($eventId, $questionId)
    {
        $event = Event::findOrFail($eventId);
        $question = EventQuestion::where('event_id', $eventId)
            ->where('id', $questionId)
            ->firstOrFail();

        return view('admin.questions.create', compact('event', 'question'));
    }

    /**
     * Memperbarui data soal di database
     */
    public function update(Request $request, $eventId, $questionId)
    {
        $event = Event::findOrFail($eventId);
        $question = EventQuestion::where('event_id', $eventId)
            ->where('id', $questionId)
            ->firstOrFail();

        $request->validate([
            'type'           => 'required|array|min:1',
            'type.*'         => 'in:pretest,posttest',
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2',
            'options.a'      => 'required|string',
            'options.b'      => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d,e',
        ]);

        // Pada update, ambil jenis ujian pertama dari pilihan checkbox
        $question->update([
            'type'           => $request->type[0],
            'question_text'  => $request->question_text,
            'options'        => array_filter($request->options),
            'correct_answer' => strtolower($request->correct_answer),
        ]);

        return redirect()->route('admin.events.show', $event->id)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Menghapus butir soal dari bank soal
     */
    public function destroy($eventId, $questionId)
    {
        $question = EventQuestion::where('event_id', $eventId)
            ->where('id', $questionId)
            ->firstOrFail();

        $question->delete();

        return redirect()->route('admin.events.show', $eventId)
            ->with('success', 'Soal berhasil dihapus!');
    }
}