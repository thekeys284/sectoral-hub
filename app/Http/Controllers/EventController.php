<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Kegiatan;

class EventController extends Controller
{
    public function index(){
        $events = Event::latest()->get();
        return view('admin.index', compact('events'));  
    }

    public function create() {
        $event = new Event();
        return view('admin.form', compact('event'));
    }

    public function store(Request $request){
        $validatedData = $request->validate([ // Tangkap hasil validasi ke $validatedData
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'lokasi_event' => 'nullable|string|max:255', // Dibuat nullable sesuai form
            'category' => 'required|string',
            'deskripsi' => 'nullable|string', // Ditambahkan dari form
            'meeting_link' => 'nullable|url', // Ditambahkan dari form
            'link_materi' => 'nullable|url', // Ditambahkan dari form
            'absensi_start' => 'nullable|date', // Ditambahkan dari form
            'absensi_end' => 'nullable|date|after_or_equal:absensi_start', // Ditambahkan dari form
            'passing_grade' => 'nullable|integer|min:0|max:100', // Ditambahkan dari form
            'posttest_password' => 'nullable|string|max:255', // Ditambahkan dari form
            'is_active' => 'boolean', // Cukup boolean, karena checkbox tidak akan ada di request jika tidak dicentang
            'image_banner'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'virtual_bg'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validatedData['created_by'] = Auth::id();

        // Handle checkbox is_active: jika tidak ada di request, berarti tidak dicentang
        if (!isset($validatedData['is_active'])) {
            $validatedData['is_active'] = false;
        }

        // Handle file upload untuk image_banner
        if ($request->hasFile('image_banner')) {
            $validatedData['image_banner'] = $request->file('image_banner')->store('events', 'public');
        }

        // Handle file upload untuk virtual_bg
        if ($request->hasFile('virtual_bg')) {
            $validatedData['virtual_bg'] = $request->file('virtual_bg')->store('events/virtual_bg', 'public');
        }

        Event::create($validatedData); // Gunakan $validatedData yang sudah divalidasi dan diproses

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event) {
        // Memuat relasi yang mungkin dibutuhkan di form edit, seperti questions atau evaluations
        // Jika form edit Anda tidak memerlukan relasi ini, baris ini bisa dihilangkan
        // $event->load(['questions', 'evaluations']);

        // Pastikan Anda mengarahkan ke view yang benar,
        // berdasarkan struktur file yang Anda berikan, form edit event ada di admin/edit.blade.php
        // Jika Anda memiliki view terpisah di data.event.form, pastikan itu yang dimaksud.
        // Saya asumsikan Anda ingin mengedit event melalui form di admin/edit.blade.php
        return view('admin.edit', compact('event'));
    }

    public function show(Event $event)
    {        
        return view('data.event.show', compact('event'));
    }

    public function update(Request $request, Event $event){
        $validatedData = $request->validate([ // Tangkap hasil validasi ke $validatedData
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'lokasi_event' => 'nullable|string|max:255', // Dibuat nullable sesuai form
            'category' => 'required|string',
            'deskripsi' => 'nullable|string', // Ditambahkan dari form
            'meeting_link' => 'nullable|url', // Ditambahkan dari form
            'link_materi' => 'nullable|url', // Ditambahkan dari form
            'absensi_start' => 'nullable|date', // Ditambahkan dari form
            'absensi_end' => 'nullable|date|after_or_equal:absensi_start', // Ditambahkan dari form
            'passing_grade' => 'nullable|integer|min:0|max:100', // Ditambahkan dari form
            'posttest_password' => 'nullable|string|max:255', // Ditambahkan dari form
            'is_active' => 'boolean', // Cukup boolean, karena checkbox tidak akan ada di request jika tidak dicentang
            'image_banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'virtual_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle checkbox is_active: jika tidak ada di request, berarti tidak dicentang
        if (!isset($validatedData['is_active'])) {
            $validatedData['is_active'] = false;
        }

        // Handle file upload untuk image_banner
        if ($request->hasFile('image_banner')) {
            // Hapus banner lama jika ada
            if ($event->image_banner) {
                Storage::disk('public')->delete($event->image_banner);
            }
            $validatedData['image_banner'] = $request->file('image_banner')->store('events', 'public');
        } else {
            // Jika tidak ada file baru diupload, dan tidak ada 'image_banner' di $validatedData,
            // pastikan tidak menghapus path yang sudah ada jika tidak ada perubahan
            // atau jika ingin menghapus jika user mengosongkan input file, bisa ditambahkan logika di sini
            unset($validatedData['image_banner']); // Pastikan tidak mencoba mengupdate dengan null jika tidak ada file baru
        }

        // Handle file upload untuk virtual_bg
        if ($request->hasFile('virtual_bg')) {
            // Hapus virtual background lama jika ada
            if ($event->virtual_bg) {
                Storage::disk('public')->delete($event->virtual_bg);
            }
            $validatedData['virtual_bg'] = $request->file('virtual_bg')->store('events/virtual_bg', 'public');
        } else {
            unset($validatedData['virtual_bg']); // Pastikan tidak mencoba mengupdate dengan null jika tidak ada file baru
        }

        $event->update($validatedData); // Gunakan $validatedData yang sudah divalidasi dan diproses

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event){
        if ($event->image_banner && file_exists(public_path($event->image_banner))) {
            unlink(public_path($event->image_banner));
            Storage::disk('public')->delete($event->image_banner); // Gunakan Storage facade
        }
        // Hapus file virtual background
        if ($event->virtual_bg && file_exists(public_path($event->virtual_bg))) {
            Storage::disk('public')->delete($event->virtual_bg); // Gunakan Storage facade
        }

        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event Berhasil Dihapus');   
    }

    public function whatsnext()
    {
        $nextEvents = Event::where('start_at', '>=', now())
                            ->orderBy('start_at', 'asc')
                            ->take(5)
                            ->get();

        $stats = [
            'total_kegiatan' => Kegiatan::count(),
            'event_active'   => Event::where('start_at', '>=', now())->count(),
        ];

        return view('pages.whatsnext', compact('nextEvents', 'stats'));
    }
}
