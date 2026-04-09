@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Monitoring Hub'])
    
    <div class="container-fluid py-4">
        {{-- CAROUSEL SECTION --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-carousel overflow-hidden h-100 p-0 shadow-lg">
                    <div id="carouselEventNext" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner border-radius-lg h-100">
                            
                            @forelse($nextEvents as $index => $event)
                            <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}" 
                                    style="background-image: url('{{ $event->image_banner ? asset($event->image_banner) : asset('img/carousel-1.jpg') }}'); background-size: cover; background-position: center;">                                
                                <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5 pb-5">
                                    <div class="icon icon-shape icon-sm bg-white text-center border-radius-md mb-3">
                                        <i class="ni ni-calendar-grid-58 text-primary opacity-10"></i>
                                    </div>
                                    <h5 class="text-white mb-1">{{ $event->title }}</h5>
                                    <p class="text-white opacity-8">
                                        <i class="ni ni-pin-3 me-1"></i> {{ $event->lokasi_event }} | 
                                        <i class="ni ni-time-alarm me-1"></i> {{ \Carbon\Carbon::parse($event->start_at)->format('d M Y') }}
                                    </p>
                                    <a href="{{ route('event.show', $event->id) }}" class="btn btn-sm btn-white mb-0">Lihat Detail Event</a>
                                </div>
                            </div>
                            @empty
                            <div class="carousel-item active h-100" style="background-image: url('{{ asset('img/carousel-1.jpg') }}'); background-size: cover;">
                                <div class="carousel-caption d-none d-md-block bottom-0 text-start start-0 ms-5 pb-5">
                                    <h5 class="text-white mb-1">Belum Ada Event Mendatang</h5>
                                    <p class="text-white opacity-8">Pantau terus Sectoral Hub untuk informasi kegiatan statistik terbaru.</p>
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

        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('css')
<style>
    .card-carousel {
        min-height: 450px;
        position: relative;
    }
    .carousel-item {
        min-height: 450px;
    }
    .carousel-item::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: linear-gradient(310deg, rgba(20, 23, 39, 0.8), rgba(58, 65, 111, 0.1));
        z-index: 1;
    }
    .carousel-caption {
        z-index: 2;
    }
</style>
@endpush

@push('js')
<script>
    var myCarousel = document.querySelector('#carouselEventNext')
    var carousel = new bootstrap.Carousel(myCarousel, {
      interval: 4000,
      ride: 'carousel'
    })
</script>
@endpush