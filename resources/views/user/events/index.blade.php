@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <!-- Banner Header -->
    <div class="card bg-emerald text-white border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative">
        <div class="card-body p-4 p-md-5 z-index-1">
            <h2 class="fw-bold text-white mb-2">Katalog Pelatihan & Kegiatan</h2>
            <p class="text-white-50 mb-0 max-width-600">
                Tingkatkan kompetensi Anda melalui berbagai program pelatihan interaktif, materi berkualitas, serta sertifikasi resmi.
            </p>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @forelse($events as $event)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                @if($event->image_banner)
                    <img src="{{ asset('storage/' . $event->image_banner) }}" alt="{{ $event->title }}" class="card-img-top style-banner">
                @else
                    <div class="bg-secondary-subtle d-flex align-items-center justify-content-center style-banner text-muted">
                        <i class="fas fa-image fa-2x"></i>
                    </div>
                @endif

                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-soft-info text-info text-capitalize px-3 py-1 rounded-pill">
                            {{ $event->category }}
                        </span>
                        <small class="text-muted"><i class="fas fa-question-circle me-1"></i>{{ $event->questions_count ?? 0 }} Soal</small>
                    </div>

                    <h5 class="fw-bold text-dark mb-2 line-clamp-2">{{ $event->title }}</h5>
                    <p class="text-muted small mb-3 line-clamp-3">{{ $event->deskripsi ?? 'Tidak ada deskripsi singkat.' }}</p>

                    <div class="mt-auto pt-3 border-top">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt text-secondary width-20 me-2"></i>
                            <small class="text-muted">
                                {{ $event->start_at ? $event->start_at->translatedFormat('d M Y, H:i') : 'Jadwal belum diatur' }} WIB
                            </small>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-map-marker-alt text-secondary width-20 me-2"></i>
                            <small class="text-muted">{{ $event->lokasi_event ?? 'Online' }}</small>
                        </div>

                        <a href="{{ route('user.events.show', $event->id) }}" class="btn btn-emerald text-white w-100 rounded-3 fw-bold">
                            Lihat Detail Event <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h5 class="text-muted fw-bold">Belum Ada Event Tersedia</h5>
            <p class="text-muted small">Silakan kembali lagi nanti untuk melihat pelatihan terbaru.</p>
        </div>
    @endforelse

<style>
    .bg-emerald { background-color: #0d9488 !important; }
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .bg-soft-info { background-color: #e0f2fe; }
    .style-banner { height: 180px; object-fit: cover; }
    .width-20 { width: 20px; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>
@endsection