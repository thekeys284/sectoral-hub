@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 text-center overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    
                    {{-- Ganti $result->is_passed menjadi $isPassed --}}
                    @if($isPassed || $type === 'pretest')
                        <div class="mb-3 text-success">
                            <i class="fas fa-check-circle fa-4x"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">
                            {{ $type === 'pretest' ? 'Pretest Selesai!' : 'Selamat, Anda LULUS!' }}
                        </h3>
                        <p class="text-muted small mb-4">
                            {{ $type === 'pretest' ? 'Terima kasih telah menyelesaikan ujian pretest.' : 'Anda berhasil melampaui passing grade pelatihan ini.' }}
                        </p>
                    @else
                        <div class="mb-3 text-danger">
                            <i class="fas fa-times-circle fa-4x"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Belum Memenuhi Passing Grade</h3>
                        <p class="text-muted small mb-4">
                            Nilai Anda belum mencapai kriteria minimal kelulusan (Passing Grade: {{ $event->passing_grade }}).
                        </p>
                    @endif

                    <!-- Box Nilai Skor (Ganti $result->score menjadi $score) -->
                    <div class="p-4 bg-light rounded-4 mb-4">
                        <span class="text-muted small d-block mb-1">Nilai Ujian {{ ucfirst($type) }} Anda</span>
                        <h1 class="fw-bold text-emerald mb-0 display-4">{{ $score }}</h1>
                        <span class="text-muted small">dari skala 100</span>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('user.events.index') }}" class="btn btn-emerald text-white rounded-3 fw-bold px-4 py-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pelatihan Saya
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-emerald { color: #0d9488; }
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
</style>
@endsection