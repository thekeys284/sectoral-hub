@extends('layouts.app')

@section('content')
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                @include('layouts.navbars.guest.navbar')
            </div>
        </div>
    </div>
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain shadow-none border-0">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">Buat Password Baru</h4>
                                    <p class="mb-0 text-sm text-secondary">Silakan masukkan kata sandi baru untuk akun Anda.</p>
                                </div>
                                <div class="card-body">
                                    <form role="form" method="POST" action="{{ route('password.store') }}">
                                        @csrf
                                        {{-- Token rahasia dari URL --}}
                                        <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

                                        {{-- Input Email (Readonly) --}}
                                        <div class="flex flex-col mb-3">
                                            <label class="form-label text-xs font-weight-bold">Email</label>
                                            <input type="email" 
                                                name="email" 
                                                class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                                value="{{ $email ?? request()->query('email') ?? old('email') }}" 
                                                required 
                                                readonly>
                                            @error('email') 
                                                <p class="text-danger text-xs pt-1 mb-0">{{ $message }}</p> 
                                            @enderror
                                        </div>

                                        {{-- Input Password Baru --}}
                                        <div class="flex flex-col mb-3">
                                            <label class="form-label text-xs font-weight-bold">Password Baru</label>
                                            <input type="password" 
                                                name="password" 
                                                class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                                placeholder="Minimal 6 karakter" 
                                                required 
                                                autofocus>
                                            @error('password') 
                                                <p class="text-danger text-xs pt-1 mb-0">{{ $message }}</p> 
                                            @enderror
                                        </div>

                                        {{-- Konfirmasi Password Baru --}}
                                        <div class="flex flex-col mb-3">
                                            <label class="form-label text-xs font-weight-bold">Konfirmasi Password Baru</label>
                                            <input type="password" 
                                                name="password_confirmation" 
                                                class="form-control form-control-lg" 
                                                placeholder="Ulangi password baru" 
                                                required>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg bg-gradient-primary w-100 mt-3 mb-0">
                                                <i class="fas fa-key me-1"></i> Simpan Password Baru
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Kanan Branding --}}
                        <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                style="background-image: url('{{ asset('img/thumbnail.jpeg') }}'); background-size: cover;">
                            </div>
                            <h4 class="mt-5 text-dark font-weight-bolder position-relative">"Empowering Data, Strengthening OPD."</h4>
                            <p class="text-secondary position-relative">Solusi All-in-One Monitoring & Pembinaan Statistik Sektoral.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection