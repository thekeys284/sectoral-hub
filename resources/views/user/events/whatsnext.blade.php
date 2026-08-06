@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => "What's Next - Katalog Kegiatan"])
    
    <div class="container-fluid py-4">
        
        {{-- ALERT NOTIFIKASI --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 text-white" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 text-white" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- CAROUSEL HIGHLIGHT SECTION --}}
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card card-carousel overflow-hidden h-100 p-0 shadow-lg border-radius-xl">
                    <div id="carouselEventNext" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner border-radius-xl h-100">
                            
                            @forelse($nextEvents as $index => $event)
                            @php
                                $isRegistered = isset($registeredEventIds) ? in_array($event->id, $registeredEventIds) : false;
                                $isClosed = $event->end_at ? \Carbon\Carbon::parse($event->end_at)->isPast() : false;
                            @endphp
                            <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}" 
                                    style="background-image: url('{{ $event->image_banner ? asset('storage/' . $event->image_banner) : asset('img/carousel-1.jpg') }}'); background-size: cover; background-position: center;">                                
                                <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5 pb-5">
                                    <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
                                        <i class="ni ni-calendar-grid-58 text-emerald opacity-10"></i>
                                    </div>
                                    <span class="badge bg-emerald text-white mb-2 text-capitalize px-3 py-1">{{ $event->category }}</span>
                                    <h4 class="text-white mb-1 fw-bold">{{ $event->title }}</h4>
                                    <p class="text-white opacity-8 mb-3">
                                        <i class="ni ni-pin-3 me-1"></i> {{ $event->lokasi_event ?? 'Online' }} | 
                                        <i class="ni ni-time-alarm me-1"></i> {{ \Carbon\Carbon::parse($event->start_at)->translatedFormat('d M Y, H:i') }} WIB
                                    </p>
                                    
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('user.events.show', $event->id) }}" class="btn btn-sm btn-white mb-0 rounded-3">
                                            <i class="fas fa-info-circle me-1"></i> Detail Event
                                        </a>

                                        @if($isRegistered)
                                            <span class="badge bg-success border border-white px-3 py-2 rounded-3 ms-2">
                                                <i class="fas fa-check-circle me-1"></i> Sudah Terdaftar
                                            </span>
                                        @elseif($isClosed)
                                            <span class="badge bg-secondary border border-white px-3 py-2 rounded-3 ms-2">
                                                <i class="fas fa-lock me-1"></i> Pendaftaran Sudah Ditutup
                                            </span>
                                        @else
                                            <form action="{{ route('user.events.register', $event->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-emerald mb-0 text-white rounded-3 fw-bold">
                                                    <i class="fas fa-user-plus me-1"></i> Daftar Kegiatan Sekarang
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="carousel-item active h-100" style="background-image: url('{{ asset('img/carousel-1.jpg') }}'); background-size: cover;">
                                <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5 pb-5">
                                    <h5 class="text-white mb-1">Belum Ada Event Mendatang</h5>
                                    <p class="text-white opacity-8">Pantau terus platform ini untuk informasi kegiatan pelatihan terbaru.</p>
                                </div>
                            </div>
                            @endforelse

                        </div>

                        <button class="carousel-control-prev w-5 me-3" type="button" data-bs-target="#carouselEventNext" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next w-5 me-3" type="button" data-bs-target="#carouselEventNext" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRID KATALOG PELATIHAN --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-0"><i class="fas fa-graduation-cap text-emerald me-2"></i>Daftar Pelatihan & Kegiatan Mendatang</h4>
                <p class="text-muted small mb-0">Pilih pelatihan yang ingin Anda ikuti dan klik pendaftaran langsung.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            @forelse($nextEvents as $event)
                @php
                    $isRegistered = isset($registeredEventIds) ? in_array($event->id, $registeredEventIds) : false;
                    $isClosed = $event->end_at ? \Carbon\Carbon::parse($event->end_at)->isPast() : false;
                @endphp
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

                                {{-- STATUS PENDAFTARAN: Terdaftar / Sudah Ditutup / Terbuka --}}
                                @if($isRegistered)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-uppercase">
                                        <i class="fas fa-check me-1"></i>Terdaftar
                                    </span>
                                @elseif($isClosed)
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 text-uppercase">
                                        <i class="fas fa-lock me-1"></i>Sudah Ditutup
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 text-uppercase">
                                        Terbuka
                                    </span>
                                @endif
                            </div>

                            <h5 class="fw-bold text-dark mb-2 line-clamp-2">{{ $event->title }}</h5>
                            <p class="text-muted small mb-3 line-clamp-3">{{ $event->deskripsi ?? 'Tidak ada deskripsi singkat.' }}</p>

                            <div class="mt-auto pt-3 border-top">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-secondary width-20 me-2"></i>
                                    <small class="text-muted">
                                        {{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->translatedFormat('d M Y, H:i') : '-' }} WIB
                                    </small>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-map-marker-alt text-secondary width-20 me-2"></i>
                                    <small class="text-muted">{{ $event->lokasi_event ?? 'Online' }}</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('user.events.show', $event->id) }}" class="btn btn-outline-secondary btn-sm rounded-3 flex-fill fw-bold mb-0">
                                        Detail
                                    </a>

                                    @if($isRegistered)
                                        <a href="{{ route('user.events.index') }}" class="btn btn-success btn-sm rounded-3 flex-fill fw-bold mb-0">
                                            <i class="fas fa-play me-1"></i> Masuk
                                        </a>
                                    @elseif($isClosed)
                                        <button type="button" class="btn btn-secondary btn-sm rounded-3 flex-fill fw-bold mb-0" disabled>
                                            <i class="fas fa-lock me-1"></i> Pendaftaran Sudah Ditutup
                                        </button>
                                    @else
                                        <form action="{{ route('user.events.register', $event->id) }}" method="POST" class="flex-fill">
                                            @csrf
                                            <button type="submit" class="btn btn-emerald text-white btn-sm w-100 rounded-3 fw-bold mb-0" onclick="return confirm('Apakah Anda yakin ingin mendaftar ke kegiatan ini?')">
                                                <i class="fas fa-user-plus me-1"></i> Daftar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted fw-bold">Belum Ada Pelatihan Tersedia</h5>
                    <p class="text-muted small mb-0">Silakan cek kembali secara berkala untuk jadwal kegiatan terbaru.</p>
                </div>
            @endforelse
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('css')
<style>
    .card-carousel {
        min-height: 400px;
        position: relative;
    }
    .carousel-item {
        min-height: 400px;
    }
    .carousel-item::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: linear-gradient(310deg, rgba(20, 23, 39, 0.85), rgba(58, 65, 111, 0.2));
        z-index: 1;
    }
    .carousel-caption {
        z-index: 2;
    }
    .btn-emerald { background-color: #0d9488; border-color: #0d9488; }
    .btn-emerald:hover { background-color: #0f766e; }
    .text-emerald { color: #0d9488; }
    .bg-emerald { background-color: #0d9488 !important; }
    .bg-soft-info { background-color: #e0f2fe; }
    .style-banner { height: 180px; object-fit: cover; }
    .width-20 { width: 20px; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>
@endpush

@push('js')
<script>
    var myCarousel = document.querySelector('#carouselEventNext')
    if (myCarousel) {
        var carousel = new bootstrap.Carousel(myCarousel, {
            interval: 4000,
            ride: 'carousel'
        })
    }
</script>
@endpush