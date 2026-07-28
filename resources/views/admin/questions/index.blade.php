@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.events.show', $event->id) }}" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Event
            </a>
            <h3 class="fw-bold mt-1 mb-0">Bank Soal Ujian</h3>
            <p class="text-muted small mb-0">Event: <strong>{{ $event->title }}</strong></p>
        </div>
        <a href="{{ route('admin.events.questions.create', $event->id) }}" class="btn btn-emerald text-white rounded-3 fw-bold">
            <i class="fas fa-plus me-1"></i> Tambah Soal Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Ringkasan Statistik Soal -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block fw-semibold">Total Soal Terdaftar</small>
                <h4 class="fw-bold mb-0 text-dark">{{ $questions->count() }} <small class="fs-6 text-muted fw-normal">Soal</small></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block fw-semibold">Soal Pretest</small>
                <h4 class="fw-bold mb-0 text-warning">
                    {{ $questions->where('type', 'pretest')->count() }}
                    <small class="fs-6 text-muted fw-normal">Soal</small>
                </h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block fw-semibold">Soal Posttest</small>
                <h4 class="fw-bold mb-0 text-primary">
                    {{ $questions->where('type', 'posttest')->count() }}
                    <small class="fs-6 text-muted fw-normal">Soal</small>
                </h4>
            </div>
        </div>
    </div>

    <!-- Daftar Soal -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="fw-bold mb-0">Daftar Pertanyaan Ujian</h5>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="accordion accordion-flush" id="accordionQuestions">
                @forelse($questions as $index => $q)
                    <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                        <h2 class="accordion-header" id="heading{{ $q->id }}">
                            <button class="accordion-button collapsed px-3 py-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $q->id }}">
                                <div class="d-flex align-items-center w-100 me-3">
                                    <span class="badge {{ $q->type == 'pretest' ? 'bg-warning text-dark' : 'bg-primary text-white' }} me-3 text-uppercase px-2 py-1">
                                        {{ $q->type }}
                                    </span>
                                    <span class="fw-bold text-dark me-2">#{{ $index + 1 }}</span>
                                    <span class="text-dark text-truncate" style="max-width: 65%;">{{ $q->question_text }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $q->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionQuestions">
                            <div class="accordion-body p-4 bg-white">
                                <!-- Teks Pertanyaan -->
                                <p class="fw-bold text-dark fs-6 mb-3">{{ $q->question_text }}</p>

                                <!-- Pilihan Jawaban (A s/d E) -->
                                <div class="row g-2 mb-3">
                                    @if(is_array($q->options) || is_object($q->options))
                                        @foreach($q->options as $key => $opt)
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 d-flex justify-content-between align-items-center {{ strtolower($key) == strtolower($q->correct_answer) ? 'bg-success-subtle border-success fw-bold text-success' : 'bg-light text-dark' }}">
                                                    <div>
                                                        <span class="badge {{ strtolower($key) == strtolower($q->correct_answer) ? 'bg-success text-white' : 'bg-secondary text-white' }} me-2">
                                                            {{ strtoupper($key) }}
                                                        </span>
                                                        {{ $opt }}
                                                    </div>
                                                    @if(strtolower($key) == strtolower($q->correct_answer))
                                                        <i class="fas fa-check-circle fs-5"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Tombol Aksi: Edit & Hapus (Dipastikan berada di dalam accordion-body) -->
                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('admin.events.questions.edit', [$event->id, $q->id]) }}" class="btn btn-outline-warning btn-sm rounded-3">
                                        <i class="fas fa-edit me-1"></i> Edit Soal
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.events.questions.destroy', [$event->id, $q->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 d-block text-secondary"></i>
                        Belum ada soal Pretest maupun Posttest yang ditambahkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .bg-success-subtle { background-color: #d1e7dd !important; }
</style>
@endsection