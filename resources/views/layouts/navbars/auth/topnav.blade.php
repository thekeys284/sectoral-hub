<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl
        {{ str_contains(Request::url(), 'virtual-reality') == true ? ' mt-3 mx-3 bg-primary' : '' }}" id="navbarBlur"
        data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $title }}</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">{{ $title }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" placeholder="Type here...">
                </div>
            </div>
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item d-flex align-items-center">
                    <form role="form" method="post" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="nav-link text-white font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none">Log out</span>
                        </a>
                    </form>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item px-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white p-0">
                        <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                    </a>
                </li>
                
                {{-- DI SINI TEMPATNYA: DROPDOWN SWITCH ROLE --}}
                @php
                    // Ambil daftar role milik user yang login dan role yang sedang aktif
                    $userRoles = auth()->check() ? (is_string(auth()->user()->role) ? json_decode(auth()->user()->role, true) ?? [auth()->user()->role] : (array) auth()->user()->role) : [];
                    $activeRole = session('active_role', $userRoles[0] ?? '');
                    $originOpd = auth()->check() ? auth()->user()->origin_opd : null;
                    $instansiOpd = ($originOpd && $originOpd->parent) ? $originOpd->parent : $originOpd;

                    $roleLabels = [
                        'operator' => 'Akun Pribadi (Operator)',
                        'produsen' => 'Akun Instansi (Produsen)',
                        'walidata' => 'Walidata (Kominfo)',
                        'pembina'  => 'Pembina (BPS)',
                        'admin'    => 'Administrator',
                    ];
                @endphp
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white p-0 d-flex align-items-center" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="badge bg-white text-dark me-1 d-none d-sm-inline-flex align-items-center px-2 py-1" style="font-size: 0.7rem;">
                            <i class="fa fa-exchange text-primary me-1"></i>
                            {{ $roleLabels[$activeRole] ?? strtoupper($activeRole) }}
                        </span>
                        <i class="fa fa-exchange cursor-pointer d-sm-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Switch Akun / Role"></i>
                    </a>
                    
                    {{-- Form Switch Role / Mode Akun --}}
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4 shadow-lg" aria-labelledby="dropdownMenuButton" style="min-width: 260px;">
                        <li class="dropdown-header text-uppercase text-xs font-weight-bolder opacity-7 text-dark mb-1">
                            Pilih Mode Akun / Role Aktif
                        </li>
                        @if($originOpd)
                            <li class="px-3 pb-2 mb-2 border-bottom">
                                <div class="text-xxs text-muted">Satker Asal: <strong class="text-dark">{{ $originOpd->name }}</strong></div>
                                @if($originOpd->parent)
                                    <div class="text-xxs text-info"><i class="fas fa-sitemap me-1"></i>Instansi Induk: <strong>{{ $originOpd->parent->name }}</strong></div>
                                @endif
                            </li>
                        @endif

                        @foreach($userRoles as $role)
                            <li class="mb-1">
                                <form action="{{ route('role.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="target_role" value="{{ $role }}">
                                    
                                    <button type="submit" class="dropdown-item border-radius-md d-flex align-items-center justify-content-between py-2 {{ $activeRole === $role ? 'bg-gray-100 font-weight-bold text-primary' : '' }}">
                                        <div class="d-flex flex-column text-start">
                                            <div class="d-flex align-items-center">
                                                <i class="ni ni-badge text-sm me-2 {{ $activeRole === $role ? 'text-primary' : 'text-secondary' }}"></i>
                                                <span class="text-xs font-weight-bold">{{ $roleLabels[$role] ?? strtoupper($role) }}</span>
                                            </div>
                                            @if($role === 'produsen' && $instansiOpd)
                                                <small class="text-xxs text-muted ms-4 text-wrap">{{ $instansiOpd->name }}</small>
                                            @elseif($role === 'operator' && $originOpd)
                                                <small class="text-xxs text-muted ms-4 text-wrap">Pelatihan ({{ $originOpd->name }})</small>
                                            @endif
                                        </div>
                                        @if($activeRole === $role)
                                            <i class="fas fa-check text-xs text-primary ms-2"></i>
                                        @endif
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </li>
                {{-- END DROPDOWN SWITCH ROLE --}}
                
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
