@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Tombol Kembali -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.events.index') }}" class="text-decoration-none text-secondary text-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Event
            </a>
            <h3 class="font-weight-bold mt-1 mb-0">{{ $event->title }}</h3>
            <span class="badge badge-sm bg-gradient-info text-capitalize px-3 py-1 mt-2">
                {{ $event->category }}
            </span>
            @if($event->is_active)
                <span class="badge badge-sm bg-gradient-success px-2 py-1 ms-1">Aktif</span>
            @else
                <span class="badge badge-sm bg-gradient-secondary px-2 py-1 ms-1">Draft</span>
            @endif
        </div>
        <div>
            <a href="{{ route('admin.events.rekap', $event->id) }}" class="btn btn-sm bg-gradient-success me-2">
                <i class="fas fa-chart-line me-1"></i> Lihat Rekap Nilai
            </a>
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-1"></i> Edit Event
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Informasi Event -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold mb-3">Detail Pelatihan</h5>
                    
                    @if($event->image_banner)
                        <img src="{{ asset('storage/' . $event->image_banner) }}" alt="Banner" class="img-fluid mb-3 w-100 style-banner">
                    @endif

                    <p class="text-secondary text-sm mb-3">{{ $event->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-map-marker-alt text-secondary width-20 me-2"></i>
                        <small class="text-dark">{{ $event->lokasi_event ?? 'Online' }}</small>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar-alt text-secondary width-20 me-2"></i>
                        <small class="text-dark">
                            {{ $event->start_at ? $event->start_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                        </small>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-users text-secondary width-20 me-2"></i>
                        <small class="text-dark"><strong>{{ $event->registrations_count }}</strong> Peserta Terdaftar</small>
                    </div>

                    <hr class="my-3">

                    <div class="mb-2">
                        <small class="text-muted d-block font-weight-bold">Link Zoom / Meeting:</small>
                        @if($event->meeting_link)
                            <a href="{{ $event->meeting_link }}" target="_blank" class="text-truncate d-block small">{{ $event->meeting_link }}</a>
                        @else
                            <span class="text-secondary text-sm">-</span>
                        @endif
                    </div>

                    <div class="mb-2">
                        <small class="text-muted d-block font-weight-bold">Link Materi / Stream:</small>
                        @if($event->link_materi)
                            <a href="{{ $event->link_materi }}" target="_blank" class="text-truncate d-block small">{{ $event->link_materi }}</a>
                        @else
                            <span class="text-secondary text-sm">-</span>
                        @endif
                    </div>

                    <!-- LINK SERTIFIKAT PELATIHAN -->
                    <div class="mb-0">
                        <small class="text-muted d-block font-weight-bold">Link Sertifikat / Drive:</small>
                        @if($event->certificate_link)
                            <a href="{{ $event->certificate_link }}" target="_blank" class="text-truncate d-block small text-success font-weight-bold">
                                <i class="fas fa-external-link-alt me-1"></i> Buka Link Sertifikat
                            </a>
                        @else
                            <span class="text-secondary text-sm">-</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 bg-light">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold mb-3 text-success"><i class="fas fa-cog me-1"></i> Parameter Kelulusan</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-sm text-secondary">Passing Grade:</span>
                        <span class="font-weight-bold small">{{ $event->passing_grade }} / 100</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-sm text-secondary">Password Posttest:</span>
                        <span class="badge bg-dark text-white font-monospace">{{ $event->posttest_password ?? 'Tanpa Password' }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-0">
                        <span class="text-sm text-secondary">Jadwal Absensi:</span>
                        <span class="text-sm text-dark font-weight-bold text-end">
                            @if($event->absensi_start && $event->absensi_end)
                                {{ $event->absensi_start->format('H:i') }} - {{ $event->absensi_end->format('H:i') }} WIB
                            @else
                                Belum diatur
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tabs Soal & Evaluasi -->
        <div class="col-lg-8">
            <ul class="nav nav-pills custom-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4" id="pills-soal-tab" data-bs-toggle="pill" data-bs-target="#pills-soal" type="button" role="tab">
                        <i class="fas fa-file-alt me-1"></i> Bank Soal Pre/Posttest ({{ $event->questions->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4" id="pills-eval-tab" data-bs-toggle="pill" data-bs-target="#pills-eval" type="button" role="tab">
                        <i class="fas fa-poll me-1"></i> Instrumen Evaluasi ({{ $event->evaluations->count() }})
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- TAB 1: BANK SOAL -->
                <div class="tab-pane fade show active" id="pills-soal" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3"> 
                                <h6 class="font-weight-bold mb-0">Daftar Pertanyaan Ujian</h6>
                                <a href="{{ route('admin.events.questions.create', $event->id) }}" class="btn btn-sm bg-gradient-success">
                                    <i class="fas fa-plus me-1"></i> Tambah Soal
                                </a>
                            </div>

                            <div class="accordion accordion-flush" id="accordionQuestions">
                                @forelse($event->questions as $index => $q)
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="heading{{ $q->id }}">
                                            <button class="accordion-button collapsed px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $q->id }}">
                                                <span class="badge {{ $q->type == 'pretest' ? 'bg-gradient-warning text-white' : 'bg-gradient-primary text-white' }} me-2 text-uppercase">
                                                    {{ $q->type }}
                                                </span>
                                                <span class="font-weight-bold text-dark">{{ $index + 1 }}. {{ Str::limit($q->question_text, 60) }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $q->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionQuestions">
                                            <div class="accordion-body px-0 pt-1 pb-3">
                                                <p class="font-weight-bold mb-2">{{ $q->question_text }}</p>
                                                
                                                <div class="row g-2 mb-3">
                                                    @if(is_array($q->options) || is_object($q->options))
                                                        @foreach($q->options as $key => $opt)
                                                            <div class="col-md-6">
                                                                <div class="p-2 border {{ strtolower($key) == strtolower($q->correct_answer) ? 'bg-success-subtle border-success font-weight-bold text-success' : 'bg-light' }}">
                                                                    {{ strtoupper($key) }}. {{ $opt }}
                                                                    @if(strtolower($key) == strtolower($q->correct_answer))
                                                                        <i class="fas fa-check-circle float-end mt-1"></i>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                                                    <a href="{{ route('admin.events.questions.edit', [$event->id, $q->id]) }}" class="btn btn-outline-warning btn-sm">
                                                        <i class="fas fa-edit me-1"></i> Edit Soal
                                                    </a>
                                                    <form action="{{ route('admin.events.questions.destroy', [$event->id, $q->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                        Belum ada soal Pretest atau Posttest yang dibuat.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: KUESIONER EVALUASI -->
                <div class="tab-pane fade" id="pills-eval" role="tabpanel">
                    
                    <!-- 1. PERTANYAAN STANDAR / MASTER -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold mb-0"><i class="fas fa-check-square text-success me-1"></i> Pertanyaan Evaluasi Standar (Template Master)</h6>
                                <span class="badge bg-gradient-success text-success border border-success">Global Template</span>
                            </div>
                            <p class="text-secondary text-sm mb-3">Centang pertanyaan standar yang ingin digunakan/diterapkan untuk event ini.</p>

                            <form action="{{ route('admin.events.evaluations.status', $event->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="list-group mb-3">
                                    @php
                                        $existingTexts = $event->evaluations->pluck('question_text')->toArray();
                                        $masterEvaluations = $masterEvaluations ?? \App\Models\EventEvaluation::whereNull('event_id')->where('is_master', true)->get();
                                    @endphp

                                    @forelse($masterEvaluations as $master)
                                        @php
                                            $isChecked = in_array($master->question_text, $existingTexts);
                                        @endphp
                                        <label class="list-group-item d-flex align-items-center gap-3 p-3 border mb-2 cursor-pointer bg-light">
                                            <input class="form-check-input flex-shrink-0" 
                                                   type="checkbox" 
                                                   name="active_evaluations[]" 
                                                   value="{{ $master->id }}" 
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            <span class="pt-1 w-100">
                                                <strong class="text-dark">{{ $master->question_text }}</strong>
                                                <small class="d-block text-muted">Bentuk: {{ $master->type == 'scale' ? 'Skala Rating (1–5)' : 'Uraian Teks' }}</small>
                                            </span>
                                            @if($isChecked)
                                                <span class="badge badge-sm bg-gradient-success">Dipakai di Event Ini</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Tidak Dipakai</span>
                                            @endif
                                        </label>
                                    @empty
                                        <div class="text-secondary text-sm py-2">Belum ada template master evaluasi di database.</div>
                                    @endforelse
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-sm bg-gradient-success font-weight-bold">
                                        <i class="fas fa-save me-1"></i> Simpan Pilihan Evaluasi Standar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 2. PERTANYAAN KHUSUS EVENT INI (is_master = 0) -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="font-weight-bold mb-0"><i class="fas fa-plus-circle text-info me-1"></i> Pertanyaan Khusus Event Ini</h6>
                                    <small class="text-muted">Pertanyaan tambahan spesifik untuk materi/narasumber event ini.</small>
                                </div>
                                <button class="btn btn-sm bg-gradient-success" data-bs-toggle="modal" data-bs-target="#modalTambahEval">
                                    <i class="fas fa-plus me-1"></i> Tambah Pertanyaan Khusus
                                </button>
                            </div>

                            <ul class="list-group list-group-flush">
                                @forelse($event->evaluations->where('is_master', false) as $eval)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                        <div>
                                            <span class="font-weight-bold text-dark me-2">#{{ $loop->iteration }}</span>
                                            <span>{{ $eval->question_text }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-sm bg-gradient-secondary text-uppercase me-2">
                                                {{ $eval->type == 'scale' ? 'Skala 1-5' : 'Teks Bebas' }}
                                            </span>

                                            <button type="button" class="btn btn-link text-warning p-0" data-bs-toggle="modal" data-bs-target="#modalEditEval{{ $eval->id }}" title="Edit Pertanyaan">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('admin.events.evaluations.destroy', [$event->id, $eval->id]) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan evaluasi khusus ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 ms-1" title="Hapus Pertanyaan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="modalEditEval{{ $eval->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title font-weight-bold">Edit Pertanyaan Evaluasi</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.events.evaluations.update', [$event->id, $eval->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body p-4 text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Butir Pertanyaan <span class="text-danger">*</span></label>
                                                            <textarea name="question_text" class="form-control" rows="3" required>{{ old('question_text', $eval->question_text) }}</textarea>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Bentuk Jawaban Peserta <span class="text-danger">*</span></label>
                                                            <select name="type" class="form-select" required>
                                                                <option value="scale" {{ old('type', $eval->type) == 'scale' ? 'selected' : '' }}>Skala Rating (1 - 5)</option>
                                                                <option value="text" {{ old('type', $eval->type) == 'text' ? 'selected' : '' }}>Teks Bebas</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm bg-gradient-success px-4 font-weight-bold">Perbarui Pertanyaan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <li class="list-group-item text-center py-4 text-muted border-0 small">
                                        Belum ada pertanyaan khusus yang ditambahkan untuk event ini.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- 3. CARD UTAMA: PERTANYAAN EVALUASI YANG DIPAKAI EVENT INI -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="font-weight-bold mb-0 text-success">
                                        <i class="fas fa-list-check me-1"></i> Pertanyaan Evaluasi yang Dipakai Event Ini
                                    </h6>
                                    <small class="text-muted">
                                        Daftar akhir kuesioner yang akan ditampilkan dan diisi oleh peserta event ini (Total: {{ $event->evaluations->count() }} pertanyaan).
                                    </small>
                                </div>
                                <span class="badge bg-gradient-success text-white">
                                    {{ $event->evaluations->count() }} Pertanyaan Aktif
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 width-20" style="width: 50px;">#</th>
                                            <th>Butir Pertanyaan Evaluasi</th>
                                            <th style="width: 140px;">Bentuk Jawaban</th>
                                            <th style="width: 120px;" class="text-center">Kategori</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($event->evaluations as $index => $activeEval)
                                            <tr>
                                                <td class="ps-3 font-weight-bold text-secondary">{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="font-weight-bold text-dark">{{ $activeEval->question_text }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm bg-gradient-secondary">
                                                        {{ $activeEval->type == 'scale' ? 'Skala Rating (1–5)' : 'Uraian Teks' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($activeEval->is_master)
                                                        <span class="badge bg-gradient-success text-success border border-success">
                                                            Standar
                                                        </span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-info">
                                                            Khusus
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-clipboard-list fa-2x mb-2 d-block text-secondary"></i>
                                                    Belum ada pertanyaan evaluasi yang diaktifkan/ditambahkan untuk event ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pertanyaan Khusus -->
<div class="modal fade" id="modalTambahEval" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Tambah Pertanyaan Evaluasi Khusus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.events.evaluations.store', $event->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Butir Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control" rows="3" required placeholder="Contoh: Bagaimana pendapat Anda mengenai penguasaan materi oleh narasumber?"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Bentuk Jawaban Peserta <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="scale">Skala Rating (1 - 5: Sangat Tidak Puas s/d Sangat Puas)</option>
                            <option value="text">Teks Bebas (Saran / Masukan Uraian)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm bg-gradient-success px-4 font-weight-bold">Simpan Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .width-20 { width: 20px; }
    .style-banner { max-height: 180px; object-fit: cover; }
    .bg-soft-info { background-color: #e0f2fe; }
    .bg-gradient-success { background-color: #0d9488; border-color: #0d9488; }
    .bg-gradient-success:hover { background-color: #0f766e; }
    .bg-gradient-success { background-color: #0d9488 !important; }
    .text-success { color: #0d9488; }
    .bg-gradient-success { background-color: #ccfbf1; }
    .border-success { border-color: #99f6e4 !important; }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection