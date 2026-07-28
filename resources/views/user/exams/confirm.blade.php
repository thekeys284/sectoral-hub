@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Back Button -->
            <a href="{{ route('user.events.show', $event->id) }}" class="text-decoration-none text-muted small d-inline-block mb-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Event
            </a>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    <span class="badge {{ $type == 'pretest' ? 'bg-warning-subtle text-warning' : 'bg-primary-subtle text-primary' }} text-uppercase px-3 py-2 rounded-pill fw-bold mb-3">
                        Ujian {{ $type }}
                    </span>

                    <h4 class="fw-bold text-dark mb-2">{{ $event->title }}</h4>
                    <p class="text-muted small mb-4">
                        @if($type == 'pretest')
                            Pretest digunakan untuk mengukur pengetahuan awal Anda sebelum materi disampaikan.
                        @else
                            Posttest digunakan sebagai syarat kelulusan pelatihan (Minimal Nilai: <strong>{{ $event->passing_grade }}</strong>).
                        @endif
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-4 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small"><i class="fas fa-question-circle me-1"></i> Jumlah Soal:</span>
                            <span class="fw-bold small text-dark">{{ $questionsCount }} Soal</span>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <span class="text-muted small"><i class="fas fa-redo me-1"></i> Kesempatan Ujian:</span>
                            <span class="fw-bold small text-dark">1 Kali Pengerjaan</span>
                        </div>
                    </div>

                    <!-- Jika Posttest Membutuhkan Password Token -->
                    @if($type == 'posttest' && $event->posttest_password)
                        <form action="{{ route('user.exams.verify_password', $event->id) }}" method="POST">
                            @csrf
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold text-dark small">Password Token Ujian <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control rounded-3 py-2" placeholder="Masukkan token dari panitia..." required autocomplete="off">
                                <small class="text-muted d-block mt-1">Minta token/password kepada panitia atau pengawas ujian.</small>
                            </div>

                            <button type="submit" class="btn btn-emerald text-white w-100 rounded-3 fw-bold py-2">
                                <i class="fas fa-key me-1"></i> Verifikasi & Mulai Posttest
                            </button>
                        </form>
                    @else
                        <!-- Jika Pretest atau Posttest Tanpa Password -->
                        <a href="{{ route('user.exams.show', [$event->id, $type]) }}" class="btn btn-emerald text-white w-100 rounded-3 fw-bold py-2">
                            <i class="fas fa-play me-1"></i> Mulai Ujian Sekarang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .bg-warning-subtle { background-color: #fef3c7; }
    .bg-primary-subtle { background-color: #e0f2fe; }
</style>
@endsection