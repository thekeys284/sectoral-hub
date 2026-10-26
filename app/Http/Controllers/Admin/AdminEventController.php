<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventEvaluation;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    private function uploadEventFile(\Illuminate\Http\UploadedFile $file, string $subFolder): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $fileName = Str::random(40) . '.' . $ext;
        $relativePath = trim($subFolder, '/') . '/' . $fileName;

        $storageDir = storage_path('app/public/' . trim($subFolder, '/'));
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }

        try {
            $file->move($storageDir, $fileName);
            @chmod($storageDir . DIRECTORY_SEPARATOR . $fileName, 0664);
            return $relativePath;
        } catch (\Throwable $e) {
            $publicDir = public_path('storage/' . trim($subFolder, '/'));
            if (!is_dir($publicDir)) {
                @mkdir($publicDir, 0777, true);
            }
            $file->move($publicDir, $fileName);
            @chmod($publicDir . DIRECTORY_SEPARATOR . $fileName, 0664);
            return $relativePath;
        }
    }

    private function deleteEventFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }
        $storageFile = storage_path('app/public/' . $relativePath);
        if (is_file($storageFile)) {
            @unlink($storageFile);
        }
        $publicFile = public_path('storage/' . $relativePath);
        if (is_file($publicFile)) {
            @unlink($publicFile);
        }
    }
    public function index(){
        $events = Event::withCount('registrations')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        return view('admin.index', compact('events'));
    }

    public function create(){
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'             => 'required|string|max:255',
            'category'          => 'required|in:pembinaan,sosialisasi,pelatihan,rapat',
            'start_at'          => 'nullable|date',
            'end_at'            => 'nullable|date|after_or_equal:start_at',
            'lokasi_event'      => 'nullable|string',
            'deskripsi'         => 'nullable|string',
            'meeting_link'      => 'nullable|url',
            'youtube_link'      => 'nullable|url',
            'link_materi'       => 'nullable|url',
            'doc_link'          => 'nullable|url',
            'certificate_type'  => 'nullable|in:all,unique',
            'certificate_link'  => 'nullable|url',
            'absensi_type'      => 'nullable|in:web,link',
            'absensi_link'      => 'nullable|url',
            'absensi_start'     => 'nullable|date',
            'absensi_end'       => 'nullable|date|after_or_equal:absensi_start',
            'passing_grade'     => 'required|integer|min:0|max:100',
            'posttest_password' => 'nullable|string|max:50',
            'image_banner'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'virtual_bg'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $validatedData['created_by'] = auth()->id();
        $validatedData['is_active'] = $request->has('is_active');

        if (Schema::hasColumn('events', 'has_pretest')) {
            $validatedData['has_pretest'] = $request->has('has_pretest');
            $validatedData['has_posttest'] = $request->has('has_posttest');
            $validatedData['has_evaluasi'] = $request->has('has_evaluasi');
        } else {
            unset($validatedData['has_pretest'], $validatedData['has_posttest'], $validatedData['has_evaluasi']);
        }

        if (Schema::hasColumn('events', 'absensi_type')) {
            $validatedData['absensi_type'] = $request->input('absensi_type', 'web');
            if ($validatedData['absensi_type'] === 'web') {
                $validatedData['absensi_link'] = null;
            }
        } else {
            unset($validatedData['absensi_type'], $validatedData['absensi_link']);
        }

        $validatedData['certificate_type'] = $request->input('certificate_type', 'all');
        if ($validatedData['certificate_type'] === 'unique') {
            $validatedData['certificate_link'] = null;
        }

        // Upload Banner
        if ($request->hasFile('image_banner')) {
            $validatedData['image_banner'] = $this->uploadEventFile($request->file('image_banner'), 'events/banners');
        }

        // Upload Virtual Background
        if ($request->hasFile('virtual_bg')) {
            $validatedData['virtual_bg'] = $this->uploadEventFile($request->file('virtual_bg'), 'events/vbg');
        }

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
            'youtube_link'      => 'nullable|url',
            'link_materi'       => 'nullable|url',
            'doc_link'          => 'nullable|url',
            'certificate_type'  => 'required|in:all,unique',
            'certificate_link'  => 'nullable|url',
            'absensi_type'      => 'nullable|in:web,link',
            'absensi_link'      => 'nullable|url',
            'absensi_start'     => 'nullable|date',
            'absensi_end'       => 'nullable|date|after_or_equal:absensi_start',
            'passing_grade'     => 'required|integer|min:0|max:100',
            'posttest_password' => 'nullable|string|max:50',
            'image_banner'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'virtual_bg'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data['is_active'] = $request->has('is_active');

        if (Schema::hasColumn('events', 'has_pretest')) {
            $data['has_pretest'] = $request->has('has_pretest');
            $data['has_posttest'] = $request->has('has_posttest');
            $data['has_evaluasi'] = $request->has('has_evaluasi');
        } else {
            unset($data['has_pretest'], $data['has_posttest'], $data['has_evaluasi']);
        }

        if (Schema::hasColumn('events', 'absensi_type')) {
            $data['absensi_type'] = $request->input('absensi_type', 'web');
            if ($data['absensi_type'] === 'web') {
                $data['absensi_link'] = null;
            }
        } else {
            unset($data['absensi_type'], $data['absensi_link']);
        }

        // Jika dipilih tipe 'unique', kosongkan certificate_link umum
        if ($data['certificate_type'] === 'unique') {
            $data['certificate_link'] = null;
        }

        // Replace Banner jika ada upload baru
        if ($request->hasFile('image_banner')) {
            $this->deleteEventFile($event->image_banner);
            $data['image_banner'] = $this->uploadEventFile($request->file('image_banner'), 'events/banners');
        }

        // Replace Virtual Background jika ada upload baru
        if ($request->hasFile('virtual_bg')) {
            $this->deleteEventFile($event->virtual_bg);
            $data['virtual_bg'] = $this->uploadEventFile($request->file('virtual_bg'), 'events/vbg');
        }

        $event->update($data);

        // Jika mode "Satu Link untuk Semua" dan link diisi, sinkronkan otomatis sertifikat_path seluruh peserta
        if ($data['certificate_type'] === 'all' && !empty($data['certificate_link'])) {
            EventRegistration::where('event_id', $event->id)
                ->update(['sertifikat_path' => $data['certificate_link']]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Data event berhasil diperbarui!');
    }

    public function destroy($id)
    { 
        $event = Event::findOrFail($id);

        // Hapus file gambar dari storage
        $this->deleteEventFile($event->image_banner);
        $this->deleteEventFile($event->virtual_bg);

        $event->delete(); 

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }

    public function rekap($id)
    {
        $event = Event::findOrFail($id);

        // Tambahkan 'user.opd' agar relasi OPD ikut ter-load (untuk Satuan Kerja & Alamat di export)
        $registrations = EventRegistration::with(['user.opd', 'evaluationAnswer'])
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
                'nip'             => $reg->user->nip ?? '-',
                'opd'             => $reg->user ? ($reg->user->origin_opd ?? $reg->user->opd) : null,
                'status_absen'    => $reg->status_kehadiran,
                'score_pretest'   => $reg->score_pretest ?? 0,
                'score_posttest'  => $reg->score_posttest ?? 0,
                'sudah_evaluasi'  => $sudahEvaluasi,
                'nilai_akhir'     => $reg->nilai_akhir,
                'is_lulus'        => $reg->isLulus(),
                'certificate_url' => $reg->certificate_url,
                'sertifikat_path' => $reg->sertifikat_path,
            ];
        });

        return view('admin.rekap', compact('event', 'rekapData'));
    }
}