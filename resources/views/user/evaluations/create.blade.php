@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <!-- Header Evaluasi -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <a href="{{ route('user.events.show', $event->id) }}" class="text-decoration-none text-muted small d-inline-block mb-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Event
            </a>
            <h4 class="fw-bold mb-1 text-emerald">Kuesioner Evaluasi Kepuasan</h4>
            <p class="text-muted small mb-0">Pelatihan: <strong>{{ $event->title }}</strong></p>
        </div>
    </div>

    <!-- Form Kuesioner Evaluasi -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <form action="{{ route('user.evaluations.store', $event->id) }}" method="POST">
                @csrf
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <p class="text-muted small mb-4">
                            Silakan berikan penilaian objektif Anda untuk membantu kami meningkatkan kualitas pelatihan di masa mendatang.
                        </p>

                        @forelse($evaluations as $index => $eval)
                            <div class="mb-4 pb-4 border-bottom">
                                <label class="form-label fw-bold text-dark d-block mb-2">
                                    {{ $index + 1 }}. {{ $eval->question_text }} <span class="text-danger">*</span>
                                </label>

                                <!-- Jawaban Bentuk Skala Rating (1-5) -->
                                @if($eval->type === 'scale')
                                    <div class="d-flex justify-content-between gap-2 max-width-500 my-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="rating-box text-center p-2 border rounded-3 cursor-pointer flex-fill">
                                                <input type="radio" name="answers[{{ $eval->id }}]" value="{{ $i }}" class="btn-check" id="eval_{{ $eval->id }}_{{ $i }}" required>
                                                <span class="d-block fw-bold fs-5">{{ $i }}</span>
                                                <small class="text-muted d-none d-md-block style-caption">
                                                    @if($i == 1) Sbg Tidak Puas
                                                    @elseif($i == 3) Cukup
                                                    @elseif($i == 5) Sangat Puas
                                                    @endif
                                                </small>
                                            </label>
                                        @endfor
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small max-width-500 px-1">
                                        <span>1 = Sangat Tidak Puas</span>
                                        <span>5 = Sangat Puas</span>
                                    </div>

                                <!-- Jawaban Bentuk Teks Uraian Bebas -->
                                @else
                                    <textarea name="answers[{{ $eval->id }}]" class="form-control rounded-3" rows="3" required placeholder="Tuliskan masukan / saran Anda di sini..."></textarea>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                Tidak ada pertanyaan kuesioner evaluasi yang perlu diisi.
                            </div>
                        @endforelse

                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('user.events.show', $event->id) }}" class="btn btn-light rounded-3 px-4">Batal</a>
                            <button type="submit" class="btn btn-emerald text-white px-4 rounded-3 fw-bold">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Evaluasi
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-emerald { color: #0d9488; }
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .cursor-pointer { cursor: pointer; }
    .max-width-500 { max-width: 500px; }
    .style-caption { font-size: 10px; }
    .rating-box { transition: all 0.2s ease; }
    .rating-box:hover { border-color: #0d9488; background-color: #f0fdf4; }
    .btn-check:checked + .rating-box, .rating-box:has(.btn-check:checked) {
        background-color: #0d9488 !important;
        color: #ffffff !important;
        border-color: #0d9488 !important;
    }
    .rating-box:has(.btn-check:checked) small { color: #ffffff !important; }
</style>
@endsection