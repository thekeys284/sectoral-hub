@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Profil Saya'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                {{-- Form Informasi Umum --}}
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Informasi Profil</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}">
                                        @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>No. HP</label>
                                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role Dimiliki</label>
                                        <input type="text" class="form-control" value="{{ strtoupper(implode(', ', $roles)) }}" disabled>
                                        <small class="text-muted">*Role Aktif Saat Ini: <strong>{{ strtoupper($activeRole) }}</strong></small>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>

                {{-- Form Ganti Password --}}
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Keamanan (Ganti Password)</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="form-group">
                                <label>Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm">Ganti Password</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Card Ringkasan Profil di Samping --}}
            <div class="col-md-4">
                @if(count($roles) > 1)
                {{-- Form Switch Role (Jika memiliki lebih dari 1 role) --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Ganti Mode Role Aktif</h6>
                        <form action="{{ route('role.switch') }}" method="POST">
                            @csrf
                            <div class="d-flex align-items-center">
                                <select name="target_role" class="form-control me-3">
                                    @foreach($roles as $r)
                                        <option value="{{ $r }}" {{ $activeRole === $r ? 'selected' : '' }}>
                                            {{ strtoupper($r) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mb-0">Switch</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <div class="card card-profile">
                    <img src="{{ asset('img/bg-profile.jpg') }}" alt="Image placeholder" class="card-img-top">
                    <div class="row justify-content-center">
                        <div class="col-4 col-lg-4 order-lg-2">
                            <div class="mt-n4 mt-lg-n6 mb-4 mb-lg-0">
                                <a href="javascript:;">
                                    <img src="{{ asset('img/team-1.jpg') }}" class="rounded-circle img-fluid border border-2 border-white">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="text-center mt-4">
                            <h5>{{ $user->name }}</h5>
                            <div class="h6 mt-4">
                                <i class="ni ni-briefcase-24 mr-2"></i>Role Aktif: {{ ucfirst($activeRole) }}
                            </div>
                        </div>
                    </div>
                </div>
            @if($activeRole === 'pembina')
                <div class="card card-profile pt-0 mt-4">
                    <div class="card-body pt-0">
                        <div class="text-center mt-4">
                            <h5>Daftar Dinas Binaan</h5>
                        </div>
                        <ul class="list-group list-group-flush mt-3 text-start">
                            @forelse($opdBinaan as $opd)
                                <li class="list-group-item px-0 border-0 mb-2">
                                    <div class="h6 mb-1 text-sm"><i class="ni ni-building me-2"></i>{{ $opd->name }}</div>
                                    @if(isset($opd->pic) && $opd->pic->count() > 0)
                                        @foreach($opd->pic as $pic)
                                            <div class="text-xs text-muted ms-4 mb-1">
                                                <i class="fas fa-user me-1"></i> {{ $pic->name }} <br>
                                                <span class="text-xs text-secondary mt-1">
                                                    <i class="fas fa-phone me-1"></i>
                                                    @if($pic && $pic->no_hp)
                                                        @php
                                                            // Bersihkan nomor dari spasi, strip, atau tanda + yang tidak sengaja terinput
                                                            $clean_phone = preg_replace('/[^0-9]/', '', $pic->no_hp);
                                                            // Jika nomor masih diawali angka 0, ubah menjadi 62
                                                            if (str_starts_with($clean_phone, '0')) {
                                                                $clean_phone = '62' . substr($clean_phone, 1);
                                                            }
                                                        @endphp
                                                        <a href="https://wa.me/{{ $clean_phone }}" target="_blank" class="text-info font-weight-bold">
                                                            {{ $pic->no_hp }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Belum ada No HP</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-xs text-muted ms-4 mb-1">
                                            <i class="fas fa-info-circle me-1"></i> Belum ada PIC Produsen
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-center text-sm text-muted">
                                    Belum ada dinas binaan.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @elseif($activeRole === 'produsen' && $user->opd)
                <div class="card card-profile pt-0 mt-4">
                    <div class="card-body pt-0">
                        <div class="text-center mt-4">
                            <h5>Informasi Pembina</h5>
                            @if($user->opd->pembina)
                                <div class="h6 mt-4 mb-0">
                                    <i class="ni ni-single-02 me-2"></i>{{ $user->opd->pembina->name }}
                                </div>
                                <div class="text-sm mt-2">
                                    <i class="ni ni-email-83 me-2"></i>{{ $user->opd->pembina->email }}
                                </div>
                                <div class="text-sm mt-2">
                                    <span class="text-xs text-secondary mt-1">
                                        <i class="fas fa-phone me-1"></i>
                                    @if($user->opd->pembina->no_hp)
                                            @php
                                                // Bersihkan nomor dari spasi, strip, atau tanda + yang tidak sengaja terinput
                                            $clean_phone = preg_replace('/[^0-9]/', '', $user->opd->pembina->no_hp);
                                                // Jika nomor masih diawali angka 0, ubah menjadi 62
                                                if (str_starts_with($clean_phone, '0')) {
                                                    $clean_phone = '62' . substr($clean_phone, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $clean_phone }}" target="_blank" class="text-info font-weight-bold">
                                            {{ $user->opd->pembina->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted">Belum ada No HP</span>
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="h6 mt-4 text-muted text-sm font-weight-normal">
                                    Belum ada Pembina yang ditugaskan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#5e72e4'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#f5365c'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Peringatan!',
                text: 'Silakan periksa kembali isian Anda.',
                confirmButtonColor: '#f5365c'
            });
        @endif
    });
</script>
@endpush
