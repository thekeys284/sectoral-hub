@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header & Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.events.show', $event->id) }}" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Event
            </a>
            <h3 class="fw-bold mt-1 mb-0">Instrumen Evaluasi Kepuasan</h3>
            <p class="text-muted small mb-0">Event: <strong>{{ $event->title }}</strong></p>
        </div>
        <button class="btn btn-emerald text-white rounded-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahEval">
            <i class="fas fa-plus me-1"></i> Tambah Pertanyaan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Ringkasan Statistik Evaluasi -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block fw-semibold">Total Pertanyaan</small>
                <h4 class="fw-bold mb-0 text-dark">{{ $evaluations->count() }} <small class="fs-6 text-muted fw-normal">Butir</small></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block fw-semibold">Pertanyaan Skala Rating (1–5)</small>
                <h4 class="fw-bold mb-0 text-emerald">
                    {{ $evaluations->where('type', 'scale')->count() }}
                    <small class="fs-6 text-muted fw-normal">Butir</small>
                </h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block fw-semibold">Pertanyaan Uraian Teks</small>
                <h4 class="fw-bold mb-0 text-info">
                    {{ $evaluations->where('type', 'text')->count() }}
                    <small class="fs-6 text-muted fw-normal">Butir</small>
                </h4>
            </div>
        </div>
    </div>

    <!-- Tabel / Daftar Instrumen Evaluasi -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="fw-bold mb-0">Daftar Pertanyaan Kuesioner</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Butir Pertanyaan</th>
                            <th style="width: 220px;">Tipe Respon</th>
                            <th class="text-end pe-4" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluations as $index => $eval)
                            <tr>
                                <td class="ps-4 text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $eval->question_text }}</span>
                                </td>
                                <td>
                                    @if($eval->type == 'scale')
                                        <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-star me-1"></i> Skala Rating (1–5)
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-comment-dots me-1"></i> Uraian / Teks
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.events.evaluations.destroy', [$event->id, $eval->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan evaluasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-poll fa-3x mb-3 d-block text-secondary"></i>
                                    Belum ada instrumen pertanyaan evaluasi yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Evaluasi -->
<div class="modal fade" id="modalTambahEval" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Pertanyaan Evaluasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.events.evaluations.store', $event->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Pertanyaan Evaluasi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Butir Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" rows="3" required placeholder="Contoh: Bagaimana pendapat Anda mengenai penguasaan materi oleh narasumber?">{{ old('question_text') }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipe Jawaban -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bentuk Jawaban Peserta <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="scale" {{ old('type') == 'scale' ? 'selected' : '' }}>Skala Rating (1 - 5: Sangat Tidak Puas s/d Sangat Puas)</option>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Teks Bebas (Saran / Masukan Uraian)</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald text-white px-4 rounded-3 fw-bold">Simpan Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .text-emerald { color: #0d9488; }
    .bg-emerald-subtle { background-color: #ccfbf1; }
    .border-emerald-subtle { border-color: #99f6e4 !important; }
</style>
@endsection