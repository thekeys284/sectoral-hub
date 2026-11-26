<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventEvaluation;
use Illuminate\Http\Request;

class AdminEvaluationController extends Controller
{
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        $evaluations = EventEvaluation::where('event_id', $eventId)->get();

        return view('admin.evaluations.index', compact('event', 'evaluations'));
    }

    /**
     * Menyimpan pertanyaan evaluasi khusus baru (is_master = 0)
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:scale,text',
        ]);

        $maxOrder = EventEvaluation::where('event_id', $event->id)->max('sort_order') ?? 0;

        EventEvaluation::create([
            'event_id'      => $event->id,
            'question_text' => $request->question_text,
            'type'          => $request->type,
            'is_master'     => false,
            'is_active'     => true,
            'sort_order'    => $maxOrder + 1,
        ]);

        return redirect()->route('admin.events.show', $event->id)
            ->with('active_tab', 'eval')
            ->with('success', 'Pertanyaan evaluasi khusus berhasil ditambahkan!');
    }

    /**
     * Memperbarui teks/tipe pertanyaan evaluasi
     */
    public function update(Request $request, $eventId, $evaluationId)
    {
        $event = Event::findOrFail($eventId);
        $evaluation = EventEvaluation::where('event_id', $eventId)->findOrFail($evaluationId);

        $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:scale,text',
        ]);

        $evaluation->update([
            'question_text' => $request->question_text,
            'type'          => $request->type,
        ]);

        return redirect()->route('admin.events.show', $event->id)
            ->with('active_tab', 'eval')
            ->with('success', 'Pertanyaan evaluasi berhasil diperbarui!');
    }

    // Di AdminEvaluationController.php
    /**
 * Memperbarui status centang (is_active) pertanyaan evaluasi master
 */
/**
 * Memperbarui status centang (is_active) pertanyaan evaluasi master
 */
    public function updateStatus(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // Array ID Master Evaluasi (event_id NULL) yang dicentang admin
        $selectedMasterIds = $request->input('active_evaluations', []);

        // 1. Ambil data teks pertanyaan master yang dicentang
        $selectedMasters = EventEvaluation::whereNull('event_id')
            ->whereIn('id', $selectedMasterIds)
            ->get();

        // Dapatkan daftar teks pertanyaan master yang terpilih
        $selectedTexts = $selectedMasters->pluck('question_text')->toArray();

        // 2. Hapus evaluasi master di event ini yang centangnya DICABUT (tidak ada di list terpilih)
        EventEvaluation::where('event_id', $event->id)
            ->where('is_master', true) // penanda bahwa ini hasil dari master
            ->whereNotIn('question_text', $selectedTexts)
            ->delete();

        // 3. Tambahkan pertanyaan master yang BARU DICENTANG (jika belum ada di event ini)
        $maxOrder = EventEvaluation::where('event_id', $event->id)->max('sort_order') ?? 0;
        foreach ($selectedMasters as $master) {
            $exists = EventEvaluation::where('event_id', $event->id)
                ->where('question_text', $master->question_text)
                ->exists();
            if (!$exists) {
                $maxOrder++;
                EventEvaluation::create([
                    'event_id'      => $event->id,
                    'question_text' => $master->question_text,
                    'type'          => $master->type,
                    'is_master'     => true,
                    'is_active'     => true,
                    'sort_order'    => $maxOrder,
                ]);
            }
        }

        return redirect()->route('admin.events.show', $event->id)
            ->with('active_tab', 'eval')
            ->with('success', 'Pertanyaan evaluasi standar untuk event ini berhasil disimpan!');
    }

    /**
     * Mengatur ulang urutan (sort_order) pertanyaan evaluasi yang dipakai event ini
     */
    public function reorder(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $request->validate([
            'ordered_ids'   => 'required|array',
            'ordered_ids.*' => 'integer',
        ]);

        foreach ($request->ordered_ids as $index => $evalId) {
            EventEvaluation::where('event_id', $event->id)
                ->where('id', $evalId)
                ->update(['sort_order' => $index + 1]);
        }

        return redirect()->route('admin.events.show', $event->id)
            ->with('active_tab', 'eval')
            ->with('success', 'Urutan nomor pertanyaan evaluasi berhasil diperbarui!');
    }

    /**
     * Menghapus pertanyaan evaluasi
     */
    public function destroy($eventId, $evaluationId)
    {
        $evaluation = EventEvaluation::where('event_id', $eventId)->findOrFail($evaluationId);
        $evaluation->delete();

        return redirect()->route('admin.events.show', $eventId)
            ->with('active_tab', 'eval')
            ->with('success', 'Pertanyaan evaluasi berhasil dihapus!');
    }
}