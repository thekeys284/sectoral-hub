@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detail Event'])
    <div class="container-fluid py-4">
        <div class="row">
            {{-- Bagian Kiri: Info Utama & Banner --}}
            <div class="col-lg-8">
                <div class="card mb-4 shadow-lg">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Informasi Event</h6>
                        <span class="badge bg-gradient-{{ $event->is_active ? 'success' : 'danger' }}">
                            {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div class="card-body">
                        {{-- Banner Display --}}
                        <div class="mb-4 text-center">
                            <img src="{{ $event->image_banner ? asset($event->image_banner) : asset('img/carousel-1.jpg') }}" 
                                 class="img-fluid border-radius-lg shadow-sm w-100" 
                                 style="max-height: 400px; object-fit: cover;" alt="Banner Event">
                        </div>

                        <h3 class="font-weight-bolder">{{ $event->title }}</h3>
                        <p class="text-sm text-secondary">{{ $event->category }} | <i class="ni ni-pin-3"></i> {{ $event->lokasi_event }}</p>
                        
                        <hr class="horizontal dark">
                        
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder opacity-6">Deskripsi Event</h6>
                        <p class="text-sm">
                            {!! nl2br(e($event->deskripsi ?? 'Tidak ada deskripsi untuk event ini.')) !!}
                        </p>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder opacity-6">Waktu Mulai</h6>
                                <p class="text-sm font-weight-bold"><i class="ni ni-calendar-grid-58 text-primary me-2"></i> {{ date('d F Y', strtotime($event->start_at)) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder opacity-6">Waktu Selesai</h6>
                                <p class="text-sm font-weight-bold"><i class="ni ni-calendar-grid-58 text-primary me-2"></i> {{ date('d F Y', strtotime($event->end_at)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Virtual Background & Navigasi --}}
            <div class="col-lg-4">
                {{-- Card Virtual Background --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header pb-0">
                        <h6>Virtual Background</h6>
                    </div>
                    <div class="card-body text-center">
                        @if($event->virtual_bg)
                            <img src="{{ asset($event->virtual_bg) }}" class="img-fluid border-radius-lg shadow mb-3" alt="VBG">
                            <a href="{{ asset($event->virtual_bg) }}" download class="btn btn-primary btn-sm w-100 mb-0">
                                <i class="ni ni-cloud-download-95 me-2"></i> Download VBG
                            </a>
                        @else
                            <div class="py-4 border border-radius-lg bg-gray-100">
                                <i class="ni ni-image text-lg mb-2"></i>
                                <p class="text-xs mb-0">Virtual Background tidak tersedia.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card Aksi / Navigasi --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-sm font-weight-bold">Aksi</h6>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('event.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="ni ni-bold-left me-2"></i> Kembali ke List
                            </a>
                            
                            @if(auth()->user()->role == 'admin')
                                <a href="{{ route('event.edit', $event->id) }}" class="btn btn-warning w-100">
                                    <i class="ni ni-settings me-2"></i> Edit Event
                                </a>
                            @endif
                        </div>
                        <hr class="horizontal dark">
                        <small class="text-muted">Dibuat pada: {{ $event->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footers.auth.footer')
    </div>
@endsection