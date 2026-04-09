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
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <input type="text" class="form-control" value="{{ strtoupper($user->role) }}" disabled>
                                        <small class="text-muted">*Role hanya bisa diubah oleh Admin/Walidata</small>
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
                            </div>
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password" class="form-control" required>
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
                            <div class="h6 font-weight-300">
                                <i class="ni ni-pin-3 mr-2"></i>Badan Pusat Statistik
                            </div>
                            <div class="h6 mt-4">
                                <i class="ni ni-briefcase-24 mr-2"></i>Role: {{ ucfirst($user->role) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection