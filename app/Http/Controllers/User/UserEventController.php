<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEventController extends Controller
{
    /**
     * Menampilkan daftar event yang tersedia untuk umum/peserta
     */
    public function index()
    {
        $events = auth()->user()->registeredEvents()
            ->withCount('questions')
            ->latest()
            ->paginate(9);

        return view('user.events.index', compact('events'));
    }

    /**
     * Menampilkan detail event beserta status pendaftaran user
     */
    public function show($id)
    {
        $event = Event::withCount('registrations')->findOrFail($id);
        
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        $isRegistered = $registration !== null;

        // Cek apakah user sudah pernah mengisi evaluasi
        $hasFilledEvaluation = false;
        if ($registration) {
            $hasFilledEvaluation = \App\Models\EventEvaluationAnswer::where('registration_id', $registration->id)->exists();
        }

        $now = \Carbon\Carbon::now('Asia/Jakarta'); // Dapatkan waktu saat ini dengan timezone yang benar

        return view('user.events.show', compact('event', 'registration', 'isRegistered', 'hasFilledEvaluation', 'now'));
    }

    /**
     * Mendaftar ke event
     */
    public function register(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // Cek apakah user sudah terdaftar di event ini
        $existingRegistration = EventRegistration::where('user_id', Auth::id())
                                                ->where('event_id', $event->id)
                                                ->first();

        if ($existingRegistration) {
            return back()->with('error', 'Anda sudah terdaftar di event ini.');
        }

        // Cek apakah pendaftaran masih dibuka.
        // Patokan: pendaftaran ditutup setelah waktu SELESAI pelatihan (end_at) terlewati.
        // (disamakan dengan logika status TERBUKA/SUDAH DITUTUP di view user.events.whatsnext,
        // supaya tidak ada kondisi tombol "Daftar" masih tampil tapi ditolak backend)
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        if ($event->end_at && $now->greaterThanOrEqualTo($event->end_at)) {
            return back()->with('error', 'Pendaftaran untuk event ini telah ditutup.');
        }

        // Buat pendaftaran baru
        EventRegistration::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'status_kehadiran' => false, // Default status kehadiran
            'score_pretest' => null,     // Default score pretest
            'score_posttest' => null,    // Default score posttest
        ]);

        return back()->with('success', 'Anda berhasil mendaftar ke event ' . $event->title . '!');
    }
    // Relationship methods like `registeredEvents()` and `registrations()` should be defined in the `User` model, not in the controller.
    // They have been removed from here to avoid confusion and incorrect usage.
    public function whatsnext()
    {
        $nextEvents = Event::where('is_active', true)
            ->orderBy('start_at', 'asc')
            ->get();

        $registeredEventIds = auth()->user()->registrations()->pluck('event_id')->toArray();

        return view('user.events.whatsnext', compact('nextEvents', 'registeredEventIds'));
    }

    public function absensi(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // 1. Ambil pendaftaran user
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // 2. Cek Jadwal Presensi
        $now = \Carbon\Carbon::now();
        if ($event->absensi_start && $now->lt(\Carbon\Carbon::parse($event->absensi_start))) {
            return back()->with('error', 'Presensi belum dibuka.');
        }

        if ($event->absensi_end && $now->gt(\Carbon\Carbon::parse($event->absensi_end))) {
            return back()->with('error', 'Sesi presensi telah berakhir.');
        }

        // 3. Update status kehadiran
        $registration->update([
            'status_kehadiran' => true,
        ]);

        return back()->with('success', 'Presensi berhasil dicatat! Terima kasih.');
    }
}