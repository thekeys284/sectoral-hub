@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">Sign In</h4>
                                    <p class="mb-0">Enter your email and password to sign in</p>
                                </div>
                                <div class="card-body">
                                    <form role="form" id="loginForm" method="POST" action="{{ route('login.perform') }}">
                                        @csrf
                                        <div class="flex flex-col mb-3">
                                            @error('email') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                            <input type="text" name="email" id="loginEmail" class="form-control form-control-lg" placeholder="Email atau Username" aria-label="Email" value="{{ old('email') }}" required autofocus>
                                        </div>
                                        <div class="flex flex-col mb-3">
                                            <div class="position-relative">
                                                <input type="password" name="password" id="loginPassword" class="form-control form-control-lg pe-5" aria-label="Password" placeholder="Password" required>
                                                <button type="button" class="btn btn-link text-secondary position-absolute top-50 end-0 translate-middle-y me-2 p-2 mb-0 toggle-password" data-target="loginPassword" tabindex="-1" title="Lihat/Sembunyikan Password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password') <p class="text-danger text-xs pt-1"> {{$message}} </p>@enderror
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="remember" type="checkbox" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" id="btnSignIn" class="btn btn-lg btn-primary w-100 mt-4 mb-0">Sign in</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-1 text-sm mx-auto">
                                        Forgot you password? Reset your password
                                        <a href="{{ route('password.request') }}" class="text-primary text-gradient font-weight-bold">here</a>
                                    </p>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-4 text-sm mx-auto">
                                        Don't have an account?
                                        <a href="{{ route('register') }}" class="text-primary text-gradient font-weight-bold">Sign up</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                                style="background-image: url('{{ asset('img/thumbnail.jpeg') }}'); background-size: cover;">
                            </div>
                            <h4 class="mt-5 text-black font-weight-bolder position-relative">"Empowering Data, Strengthening OPD."</h4>
                            <p class="text-black position-relative">Solusi All-in-One Monitoring &amp; Pembinaan Statistik Sektoral.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const btn = document.getElementById('btnSignIn');
                    if (btn) {
                        btn.click();
                    } else {
                        loginForm.submit();
                    }
                }
            });
        }

        // Toggle Lihat / Sembunyikan Password
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = document.getElementById(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                if (!input) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Cek jika ada error validasi untuk field 'email' (ini yang dikirim dari LoginRequest jika kredensial tidak cocok)
        @if ($errors->has('email'))
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: '{{ $errors->first('email') }}',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#f5365c'
            });
        @endif
    });
</script>
@endpush
