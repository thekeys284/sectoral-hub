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
                                    <h4 class="font-weight-bolder">Lupa Kata Sandi?</h4>
                                    <p class="mb-0 text-sm text-secondary">Masukkan email terdaftar untuk menerima tautan pemulihan kata sandi.</p>
                                </div>
                                <div class="card-body">
                                    {{-- Alert jika link email berhasil dikirim --}}
                                    @if (session('status'))
                                        <div class="alert alert-success text-white text-xs py-2 mb-3" role="alert">
                                            <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
                                        </div>
                                    @endif

                                    <form role="form" method="POST" action="{{ route('password.email') }}">
                                        @csrf
                                        <div class="flex flex-col mb-3">
                                            <label class="form-label text-xs font-weight-bold">Email Akun</label>
                                            <input type="email" 
                                                   name="email" 
                                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                                   placeholder="email@gmail.com" 
                                                   value="{{ old('email') }}" 
                                                   required 
                                                   autofocus>
                                            @error('email') 
                                                <p class="text-danger text-xs pt-1 mb-0">{{ $message }}</p> 
                                            @enderror
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-lg bg-gradient-primary w-100 mt-2 mb-0">
                                                <i class="fas fa-paper-plane me-1"></i> Send Reset Link
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-1 text-sm mx-auto">
                                        Sudah ingat password?
                                        <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Sign In</a>
                                    </p>
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