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

        return view('user.events.show', compact('event', 'registration', 'isRegistered', 'hasFilledEvaluation'));
    }

    /**
     * Mendaftar ke event
     */
    // di model User.php
    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'event_registrations', 'user_id', 'event_id')
                    ->withTimestamps();
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

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