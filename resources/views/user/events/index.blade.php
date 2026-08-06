@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Katalog Pelatihan'])

    <div class="container-fluid py-4">
        <!-- Banner Header (Format Card Argon) -->
        <div class="card bg-gradient-primary border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative">
            <div class="card-body p-4 p-md-5 z-index-1">
                <h3 class="text-white font-weight-bolder mb-2">Katalog Pelatihan & Kegiatan</h3>
                <p class="text-white text-sm mb-0 opacity-8" style="max-width: 600px;">
                    Tingkatkan kompetensi Anda melalui berbagai program pelatihan interaktif, materi berkualitas, serta sertifikasi resmi.
                </p>
            </div>
        </div>

        <!-- Alert Notifikasi (Format Argon) -->
        @if ($errors->any())
            <div class="alert alert-danger text-white rounded-3 mb-4" role="alert">
                <ul class="mb-0 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success text-white alert-dismissible fade show rounded-3 mb-4" role="alert">
                <span class="text-sm"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger text-white alert-dismissible fade show rounded-3 mb-4" role="alert">
                <span class="text-sm"><i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Cards Event Grid -->
        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                        @if($event->image_banner)
                            <img src="{{ asset('storage/' . $event->image_banner) }}" alt="{{ $event->title }}" class="card-img-top style-banner">
                        @else
                            <div class="bg-gray-200 d-flex align-items-center justify-content-center style-banner text-secondary">
                                <i class="fas fa-image fa-2x"></i>
                            </div>
                        @endif

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-sm bg-gradient-info text-capitalize">
                                    {{ $event->category }}
                                </span>
                                <span class="text-xs text-secondary font-weight-bold">
                                    <i class="fas fa-users me-1"></i>{{ $event->registrations_count ?? 0 }} Terdaftar
                                </span>
                            </div>

                            <h6 class="font-weight-bolder text-dark mb-2 line-clamp-2 text-wrap">{{ $event->title }}</h6>
                            <p class="text-xs text-secondary mb-3 line-clamp-3 text-wrap">{{ $event->deskripsi ?? 'Tidak ada deskripsi singkat.' }}</p>

                            <div class="mt-auto pt-3 border-top">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-secondary width-20 me-2 text-xs"></i>
                                    <span class="text-xs text-secondary font-weight-bold">
                                        {{ $event->start_at ? $event->start_at->translatedFormat('d M Y, H:i') : 'Jadwal belum diatur' }} WIB
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-map-marker-alt text-secondary width-20 me-2 text-xs"></i>
                                    <span class="text-xs text-secondary font-weight-bold">{{ $event->lokasi_event ?? 'Online' }}</span>
                                </div>

                                <a href="{{ route('user.events.show', $event->id) }}" class="btn btn-primary btn-sm w-100 mb-0 font-weight-bold">
                                    Lihat Detail Event <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-secondary mb-3"></i>
                    <h6 class="text-secondary font-weight-bold">Belum Ada Event yang Didaftar</h6>
                    <p class="text-xs text-secondary">Silakan kembali lagi nanti untuk melihat pelatihan terbaru.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $events->links() }}
        </div>
    </div>

<style>
    .style-banner { height: 180px; object-fit: cover; }
    .width-20 { width: 20px; text-align: center; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>
@endsection