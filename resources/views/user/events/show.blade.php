@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <a href="{{ route('user.events.index') }}" class="text-decoration-none text-muted small d-inline-block mb-3">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Katalog Pelatihan
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Informasi Event -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <span class="badge bg-soft-info text-info text-capitalize px-3 py-1 rounded-pill mb-3">
                        {{ $event->category }}
                    </span>
                    <h3 class="fw-bold text-dark mb-3">{{ $event->title }}</h3>

                    @if($event->image_banner)
                        <img src="{{ asset('storage/' . $event->image_banner) }}" alt="Banner" class="img-fluid rounded-3 mb-3 w-100 style-banner">
                    @endif

                    <p class="text-muted small mb-4">{{ $event->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt text-emerald width-25 me-2"></i>
                            <small class="text-dark"><strong>Jadwal:</strong> {{ $event->start_at ? $event->start_at->translatedFormat('d M Y, H:i') : '-' }} WIB</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-emerald width-25 me-2"></i>
                            <small class="text-dark"><strong>Lokasi:</strong> {{ $event->lokasi_event ?? 'Online' }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-graduation-cap text-emerald width-25 me-2"></i>
                            <small class="text-dark"><strong>Passing Grade:</strong> {{ $event->passing_grade }} / 100</small>
                        </div>
                    </div>

                    <!-- Tombol Daftar / Status Terdaftar -->
                    @if(!$isRegistered)
                        <form action="{{ route('user.events.register', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-emerald text-white w-100 rounded-3 fw-bold py-2">
                                <i class="fas fa-user-plus me-1"></i> Daftar Pelatihan Ini
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success border-0 bg-success-subtle text-success text-center rounded-3 mb-0 fw-bold small">
                            <i class="fas fa-check-circle me-1"></i> Anda Sudah Terdaftar
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tautan Penting (Zoom & Materi) - Hanya tampil jika sudah terdaftar -->
            @if($isRegistered)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-link text-emerald me-1"></i> Akses Pelatihan</h6>
                        <div class="mb-3">
                            <small class="text-muted d-block fw-semibold mb-1">Link Room / Zoom Meeting:</small>
                            @if($event->meeting_link)
                                <a href="{{ $event->meeting_link }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3 w-100 text-truncate">
                                    <i class="fas fa-video me-1"></i> Buka Zoom Meeting
                                </a>
                            @else
                                <span class="text-muted small">Belum tersedia</span>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted d-block fw-semibold mb-1">Materi Pelatihan:</small>
                            @if($event->link_materi)
                                <a href="{{ $event->link_materi }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-3 w-100 text-truncate">
                                    <i class="fas fa-file-download me-1"></i> Unduh / Lihat Materi
                                </a>
                            @else
                                <span class="text-muted small">Belum tersedia</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Alur Kegiatan & Progress Peserta -->
        <div class="col-lg-7">
            
            {{-- CARD STATUS PRESENSI (Hanya Tampil Jika Sudah Terdaftar) --}}
            @if($isRegistered && $registration)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon icon-shape bg-emerald-subtle text-emerald rounded-circle p-3">
                                <i class="fas fa-user-check fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Presensi Kehadiran Peserta</h6>
                                @if($registration->status_kehadiran)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Sudah Hadir
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill">
                                        <i class="fas fa-clock me-1"></i> Belum Presensi
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- TOMBOL AKSI PRESENSI --}}
                        <div>
                            @if($registration->status_kehadiran)
                                <button class="btn btn-success rounded-3 fw-bold mb-0" disabled>
                                    <i class="fas fa-check me-1"></i> Kehadiran Terkonfirmasi
                                </button>
                            @else
                                @php
                                    // Pastikan Timezone set ke Asia/Jakarta
                                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                                    $start = $event->absensi_start ? \Carbon\Carbon::parse($event->absensi_start, 'Asia/Jakarta') : null;
                                    $end = $event->absensi_end ? \Carbon\Carbon::parse($event->absensi_end, 'Asia/Jakarta') : null;

                                    // Logika aman: jika start/end kosong, dianggap tidak ada batasan
                                    $isAfterStart = $start ? $now->gte($start) : true;
                                    $isBeforeEnd  = $end ? $now->lte($end) : true;
                                    $isOpen       = $isAfterStart && $isBeforeEnd;
                                @endphp

                                @if($isOpen)
                                    <form action="{{ route('user.events.absensi', $event->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-emerald text-white rounded-3 fw-bold mb-0" onclick="return confirm('Konfirmasi kehadiran Anda dalam kegiatan ini?')">
                                            <i class="fas fa-pen-nib me-1"></i> Klik Isi Daftar Hadir
                                        </button>
                                    </form>
                                @elseif($start && $now->lt($start))
                                    <button class="btn btn-secondary rounded-3 mb-0" disabled>
                                        <i class="fas fa-lock me-1"></i> Presensi Buka Pukul {{ $start->format('H:i') }} WIB
                                    </button>
                                @else
                                    <button class="btn btn-danger rounded-3 mb-0" disabled>
                                        <i class="fas fa-times-circle me-1"></i> Waktu Presensi Berakhir
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-emerald"><i class="fas fa-tasks me-2"></i> Alur Kegiatan Pelatihan</h5>

                    @if(!$isRegistered)
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-lock fa-3x mb-3 text-secondary"></i>
                            <h6 class="fw-bold">Akses Terkunci</h6>
                            <p class="small">Silakan klik tombol <strong>"Daftar Pelatihan Ini"</strong> untuk membuka akses Presensi, Pretest, Posttest, dan Evaluasi.</p>
                        </div>
                    @else
                        <!-- Timeline Progress Step -->
                        <div class="list-group list-group-flush gap-3">
                            
                            <!-- STEP 1: PRETEST -->
                            <div class="list-group-item border rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-warning-subtle text-warning mb-1">Langkah 1</span>
                                        <h6 class="fw-bold mb-0">Ujian Pretest</h6>
                                        <small class="text-muted">Mengukur pemahaman awal sebelum pelatihan dimulai.</small>
                                    </div>
                                    
                                    @if($registration->score_pretest !== null)
                                        <div class="text-end">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold d-block mb-1">
                                                <i class="fas fa-check-circle me-1"></i> Selesai
                                            </span>
                                            <small class="text-muted fw-bold">Nilai: {{ $registration->score_pretest }}</small>
                                        </div>
                                    @else
                                        <a href="{{ route('user.exams.show', [$event->id, 'pretest']) }}" class="btn btn-sm btn-outline-emerald rounded-3 fw-bold">
                                            Mulai Pretest <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- STEP 2: POSTTEST -->
                            <div class="list-group-item border rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary mb-1">Langkah 2</span>
                                        <h6 class="fw-bold mb-0">Ujian Posttest</h6>
                                        <small class="text-muted">Evaluasi akhir kelulusan (Minimal nilai: {{ $event->passing_grade }}).</small>
                                    </div>

                                    @if($registration->score_posttest !== null)
                                        <div class="text-end">
                                            @if($registration->score_posttest >= $event->passing_grade)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold d-block mb-1">
                                                    <i class="fas fa-check-circle me-1"></i> LULUS
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold d-block mb-1">
                                                    <i class="fas fa-times-circle me-1"></i> BELUM LULUS
                                                </span>
                                            @endif
                                            <small class="text-muted fw-bold">Nilai: {{ $registration->score_posttest }}</small>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-emerald text-white rounded-3 fw-bold" data-bs-toggle="collapse" data-bs-target="#collapsePosttest">
                                            Kerjakan Posttest
                                        </button>
                                    @endif
                                </div>

                                <!-- Form Input Password Posttest Collapse (Hanya jika belum tes) -->
                                @if($registration->score_posttest === null)
                                    <div class="collapse mt-3 border-top pt-3" id="collapsePosttest">
                                        <form action="{{ route('user.exams.verify_password', $event->id) }}" method="POST">
                                            @csrf
                                            <label class="form-label small fw-bold">Masukkan Password Posttest dari Panitia:</label>
                                            <div class="input-group input-group-sm">
                                                <input type="password" name="password" class="form-control" required placeholder="Token / Password Posttest">
                                                <button class="btn btn-emerald text-white fw-bold" type="submit">Masuk Ujian</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- STEP 3: EVALUASI KEPUASAN -->
                            <div class="list-group-item border rounded-3 p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-info-subtle text-info mb-1">Langkah 3</span>
                                        <h6 class="fw-bold mb-0">Kuesioner Evaluasi Pelatihan</h6>
                                        <small class="text-muted">Masukan Anda sangat berharga untuk peningkatan mutu pelatihan.</small>
                                    </div>

                                    @if($hasFilledEvaluation)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> Sudah Diisi
                                        </span>
                                    @else
                                        <a href="{{ route('user.evaluations.create', $event->id) }}" class="btn btn-sm btn-outline-info rounded-3 fw-bold">
                                            Isi Evaluasi <i class="fas fa-edit ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .btn-outline-emerald { color: #0d9488; border-color: #0d9488; }
    .btn-outline-emerald:hover { background-color: #0d9488; color: #fff; }
    .text-emerald { color: #0d9488; }
    .bg-emerald-subtle { background-color: #ccfbf1; }
    .bg-soft-info { background-color: #e0f2fe; }
    .style-banner { max-height: 200px; object-fit: cover; }
    .width-25 { width: 25px; }
</style>
@endsection