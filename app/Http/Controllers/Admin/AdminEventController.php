<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventEvaluation;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Storage;

class AdminEventController extends Controller
{
    public function index(){
        $events = Event::withCount('registrations')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        return view('admin.index', compact('events'));
    }

    public function create(){
        return view('admin.create');
    }

    // public function store(Request $request){
    //     $data = $request->validate([
    //         'title'             => 'required|string|max:255',
    //         'category'          => 'required|in:pembinaan,sosialisasi,pelatihan,rapat',
    //         'start_at'          => 'nullable|date',
    //         'end_at'            => 'nullable|date|after_or_equal:start_at',
    //         'lokasi_event'      => 'nullable|string',
    //         'deskripsi'         => 'nullable|string',
    //         'meeting_link'      => 'nullable|url',
    //         'link_materi'       => 'nullable|url',
    //         'absensi_start'     => 'nullable|date',
    //         'absensi_end'       => 'nullable|date|after_or_equal:absensi_start',
    //         'passing_grade'     => 'required|integer|min:0|max:100',
    //         'posttest_password' => 'nullable|string|max:50',
    //         'image_banner'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    //         'virtual_bg'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    //     ]);
    //     $data['created_by'] = auth()->id();
    //     $data['is_active'] = $request->has('is_active');

    //     // Upload Banner 
    //     if ($request->hasFile('image_banner')) {
    //         $data['image_banner'] = $request->file('image_banner')->store('events/banners', 'public');
    //     }

    //     // Upload Virtual Background
    //     if ($request->hasFile('virtual_bg')) {
    //         $data['virtual_bg'] = $request->file('virtual_bg')->store('events/vbg', 'public');
    //     }

    //     Event::create($data);

    //     return redirect()->route('admin.events.index')
    //         ->with('success', 'Event/Pelatihan berhasil dibuat!');
    // }
    public function store(Request $request)
    {
        // 1. Simpan Event Baru
        $event = Event::create($validatedData);

        // 2. Inject Pertanyaan Master Bawaan ke tabel event_evaluations dengan event_id milik event baru ini
        $defaultEvaluations = [
            [
                'question_text' => 'Bagaimana kualitas dan kejelasan materi yang disampaikan?',
                'type'          => 'scale',
                'is_master'     => true,
                'is_active'     => true,
            ],
            [
                'question_text' => 'Bagaimana penguasaan materi dan penyampaian oleh Narasumber/Pengajar?',
                'type'          => 'scale',
                'is_master'     => true,
                'is_active'     => true,
            ],
            [
                'question_text' => 'Bagaimana kelancaran sarana dan prasarana (Zoom/Tempat/Koneksi)?',
                'type'          => 'scale',
                'is_master'     => true,
                'is_active'     => true,
            ],
            [
                'question_text' => 'Berikan saran dan masukan Anda untuk penyelenggaraan event berikutnya:',
                'type'          => 'text',
                'is_master'     => true,
                'is_active'     => true,
            ],
        ];

        foreach ($defaultEvaluations as $eval) {
            // event_id otomatis terisi ID dari $event->id
            $event->evaluations()->create($eval);
        }

        return redirect()->route('admin.events.show', $event->id)
            ->with('success', 'Event dan instrumen evaluasi bawaan berhasil dibuat!');
    }

    public function show($id)
    {
        // 1. Ambil event beserta relasinya
        $event = Event::with(['questions', 'evaluations'])->findOrFail($id);
        
        // 2. Ambil template master evaluasi global (yang event_id-nya NULL)
        $masterEvaluations = EventEvaluation::whereNull('event_id')
            ->where('is_master', true)
            ->get();

        // 3. Kirimkan $masterEvaluations ke View
        return view('admin.show', compact('event', 'masterEvaluations'));
    }

    public function edit($id)
    { 
        $event = Event::findOrFail($id);
        return view('admin.edit', compact('event'));
    }
    public function update(Request $request, $id)
    { 
        $event = Event::findOrFail($id);

        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|in:pembinaan,sosialisasi,pelatihan,rapat',
            'start_at'          => 'nullable|date',
            'end_at'            => 'nullable|date|after_or_equal:start_at',
            'lokasi_event'      => 'nullable|string',
            'deskripsi'         => 'nullable|string',
            'meeting_link'      => 'nullable|url',
            'link_materi'       => 'nullable|url',
            'absensi_start'     => 'nullable|date',
            'absensi_end'       => 'nullable|date|after_or_equal:absensi_start',
            'passing_grade'     => 'required|integer|min:0|max:100',
            'posttest_password' => 'nullable|string|max:50',
            'image_banner'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'virtual_bg'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['is_active'] = $request->has('is_active');

        // Replace Banner jika ada upload baru
        if ($request->hasFile('image_banner')) {
            if ($event->image_banner) {
                Storage::disk('public')->delete($event->image_banner);
            }
            $data['image_banner'] = $request->file('image_banner')->store('events/banners', 'public');
        }

        // Replace Virtual Background jika ada upload baru
        if ($request->hasFile('virtual_bg')) {
            if ($event->virtual_bg) {
                Storage::disk('public')->delete($event->virtual_bg);
            }
            $data['virtual_bg'] = $request->file('virtual_bg')->store('events/vbg', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Data event berhasil diperbarui!');
    }

    public function destroy($id)
    { 
        $event = Event::findOrFail($id);

        // Hapus file gambar dari storage
        if ($event->image_banner) {
            Storage::disk('public')->delete($event->image_banner);
        }
        if ($event->virtual_bg) {
            Storage::disk('public')->delete($event->virtual_bg);
        }

        $event->delete(); 

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }

    public function rekap($id)
    {
        $event = Event::findOrFail($id);

        // Gunakan nama relasi 'evaluationAnswer' sesuai yang ada di Model EventRegistration
        $registrations = EventRegistration::with(['user', 'evaluationAnswer'])
            ->where('event_id', $id)
            ->get();

        $rekapData = $registrations->map(function ($reg) use ($event) {
            // Cek keberadaan relasi dengan method optional / count()
            $sudahEvaluasi = $reg->evaluationAnswer ? $reg->evaluationAnswer->count() > 0 : false;

            return [
                'registration_id' => $reg->id,
                'name'            => $reg->user->name ?? 'N/A',
                'username'        => $reg->user->username ?? '-',
                'email'           => $reg->user->email ?? '-',
                'status_absen'    => $reg->status_kehadiran,
                'score_pretest'   => $reg->score_pretest ?? 0,
                'score_posttest'  => $reg->score_posttest ?? 0,
                'sudah_evaluasi'  => $sudahEvaluasi,
                'nilai_akhir'     => $reg->nilai_akhir,
                'is_lulus'        => $reg->isLulus(),
            ];
        });

        return view('admin.rekap', compact('event', 'rekapData'));
    }
}
