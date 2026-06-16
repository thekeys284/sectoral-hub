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
            
            {{-- LOOPING HEADER SEKSYEN MENU --}}
            @foreach($navigationMenu as $section)
                {{-- Hanya tampilkan header jika role user saat ini diizinkan di array 'roles' --}}
                @if(in_array(auth()->user()->role, $section['roles']))
                    
                    @if(!empty($section['header']))
                        <li class="nav-item mt-3">
                            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">{{ $section['header'] }}</h6>
                        </li>
                    @endif

                    {{-- LOOPING SUB-MENU DI DALAMNYA --}}
                    @foreach($section['items'] as $item)
                        <li class="nav-item">
                            <a class="nav-link {{ isset($item['active']) && $item['active'] ? 'active' : '' }}" 
                                href="{{ isset($item['is_url']) && $item['is_url'] ? $item['url'] : route($item['route']) }}"
                                {{ isset($item['is_url']) && $item['is_url'] ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                    @if(isset($item['icon']) && (str_contains($item['icon'], '.svg') || str_contains($item['icon'], '.png')))
                                        <img src="{{ asset($item['icon']) }}" alt="menu-icon" style="width: 16px; height: 16px;" class="opacity-10">
                                    @elseif(isset($item['icon']))
                                        <i class="{{ $item['icon'] }} text-sm opacity-10"></i>
                                    @endif
                                </div>
                                <span class="nav-link-text ms-1">{{ $item['title'] }}</span>
                            </a>
                        </li>
                    @endforeach

                @endif
            @endforeach

            {{-- MENU AKUN (Tetap statis karena selalu ada untuk semua user) --}}
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