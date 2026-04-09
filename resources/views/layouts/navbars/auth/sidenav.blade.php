<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}">
            <img src="{{ asset('img/logo-ct-dark.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Sectoral Hub</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main" style="height: auto;">
        <ul class="navbar-nav">
            
            {{-- DASHBOARD --}}
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'home' || Route::currentRouteName() == 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'pages.whatsnext' ? 'active' : '' }}" href="{{ route('pages.whatsnext') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">What's Next</span>
                </a>
            </li>

            {{-- MENU MONITORING --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Monitoring Data</h6>
            </li>
            
            {{-- METADATA --}}
            <li class="nav-item">
                @php
                    $isMetadataAdmin = auth()->user()->role == 'admin';
                    $metadataRoute = $isMetadataAdmin ? route('admin.metadata.index') : route('metadata.table');
                    $metadataActive = request()->routeIs('admin.metadata.*') || request()->routeIs('metadata.table');
                @endphp
                <a class="nav-link {{ $metadataActive ? 'active' : '' }}" href="{{ $metadataRoute }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-folder-17 text-info text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Metadata</span>
                </a>
            </li>

            {{-- ROMANTIK --}}
            <li class="nav-item">
                @php
                    $isRomantikAdmin = auth()->user()->role == 'admin';
                    $romantikRoute = $isRomantikAdmin ? route('admin.romantik.index') : route('romantik.table'); 
                    $romantikActive = request()->routeIs('admin.romantik.*')|| request()->routeIs('metadata.table');
                @endphp
                <a class="nav-link {{ $romantikActive ? 'active' : '' }}" href="{{ $romantikRoute }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-check-bold text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Romantik</span>
                </a>
            </li>

            {{-- MENU DATA SEKTORAL --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Data Sektoral</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}" href="{{ route('admin.kegiatan.index') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bullet-list-67 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Kegiatan Statistik</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.daftardata.*') ? 'active' : '' }}" href="{{ route('admin.daftardata.index') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-collection text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Daftar Data</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('event.*') ? 'active' : '' }}" href="{{ route('event.index') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Event BPS</span>
                </a>
            </li>

            {{-- MENU MASTER (Hanya Admin & Walidata) --}}
            @if(in_array(auth()->user()->role, ['admin', 'walidata']))
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administrasi</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.opd.*') ? 'active' : '' }}" href="{{ route('admin.opd.index') }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-building text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data OPD</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen User</span>
                    </a>
                </li>
            @endif

            {{-- MENU AKUN --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Akun</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profil Saya</span>
                </a>
            </li>
        </ul>
    </div>
</aside>