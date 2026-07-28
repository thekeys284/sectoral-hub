@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <!-- Header Lembar Ujian -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge {{ $type == 'pretest' ? 'bg-warning-subtle text-warning' : 'bg-primary-subtle text-primary' }} text-uppercase px-3 py-1 rounded-pill mb-1">
                    Ujian {{ $type }}
                </span>
                <h4 class="fw-bold mb-0">{{ $event->title }}</h4>
            </div>
            <div class="text-end">
                <span class="text-muted small d-block">Jumlah Soal</span>
                <span class="fw-bold text-dark fs-5">{{ $questions->count() }} Butir</span>
            </div>
        </div>
    </div>

    <!-- Form Lembar Jawaban -->
    <form action="{{ route('user.exams.submit', [$event->id, $type]) }}" method="POST" id="formExam" onsubmit="return confirm('Apakah Anda yakin ingin mengumpulkan ujian ini?')">
        @csrf
        <div class="row g-4">
            <!-- Kolom Daftar Soal -->
            <div class="col-lg-8">
                @forelse($questions as $index => $q)
                    <div class="card border-0 shadow-sm rounded-4 mb-4" id="soal-{{ $index + 1 }}">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-emerald text-white rounded-circle me-2 p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    {{ $index + 1 }}
                                </span>
                                <span class="fw-bold text-dark">Pertanyaan No. {{ $index + 1 }}</span>
                            </div>

                            <p class="fs-6 text-dark mb-4">{{ $q->question_text }}</p>

                            <!-- Pilihan Opsi A, B, C, D -->
                            <div class="d-flex flex-column gap-2">
                                @if(is_array($q->options) || is_object($q->options))
                                    @foreach($q->options as $key => $opt)
                                        <label class="option-card border rounded-3 p-3 d-flex align-items-center cursor-pointer">
                                            <input type="radio" 
                                                   name="answers[{{ $q->id }}]" 
                                                   value="{{ $key }}" 
                                                   class="form-check-input me-3 flex-shrink-0" 
                                                   onchange="markNavAnswered({{ $index + 1 }})" 
                                                   required>
                                            <span class="fw-bold text-uppercase me-2 width-20">{{ $key }}.</span>
                                            <span class="text-dark">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Belum ada soal untuk ujian ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Sidebar Navigasi Soal & Tombol Selesai -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-th me-1"></i> Navigasi Soal</h6>
                        
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach($questions as $index => $q)
                                <a href="#soal-{{ $index + 1 }}" id="nav-btn-{{ $index + 1 }}" class="btn btn-outline-secondary btn-sm rounded-3 width-40">
                                    {{ $index + 1 }}
                                </a>
                            @endforeach
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 small">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-emerald me-2" style="width: 12px; height: 12px; display: inline-block;"></span>
                                <span>Sudah Dijawab</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2" style="width: 12px; height: 12px; display: inline-block;"></span>
                                <span>Belum Dijawab</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-emerald text-white w-100 rounded-3 fw-bold py-2">
                            <i class="fas fa-paper-plane me-1"></i> Selesaikan & Kirim Jawaban
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Mengubah warna tombol navigasi menjadi hijau setelah soal dijawab
    function markNavAnswered(number) {
        const navBtn = document.getElementById('nav-btn-' + number);
        if (navBtn) {
            navBtn.classList.remove('btn-outline-secondary');
            navBtn.classList.add('btn-emerald', 'text-white');
        }
    }
</script>

<style>
    .bg-emerald { background-color: #0d9488 !important; }
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .cursor-pointer { cursor: pointer; }
    .option-card { transition: all 0.15s ease; }
    .option-card:hover { background-color: #f8fafc; border-color: #0d9488; }
    .option-card:has(input:checked) { background-color: #f0fdf4; border-color: #0d9488; font-weight: 600; }
    .width-20 { width: 20px; }
    .width-40 { width: 40px; }
</style>
@endsection