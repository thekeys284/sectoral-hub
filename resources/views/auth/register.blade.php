@extends('layouts.app')

@section('content')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.5rem;
        padding: 0.5rem;
        height: auto;
    }
</style>
@endpush
    <main class="main-content  mt-0">
        <div class="align-items-start  pb-11 m-3 border-radius-lg">            
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container">
                
            </div>
        </div>
        <div class="container">
            <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
                <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
                    <div class="card z-index-0">
                        <div class="card-header text-center pt-4">
                            <h5>Register</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('register.perform') }}">
                                @csrf
                                <div class="flex flex-col mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Name" aria-label="Name" value="{{ old('name') }}" >
                                    @error('name') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email" value="{{ old('email') }}" >
                                    @error('email') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="Password" aria-label="Password">
                                    @error('password') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" aria-label="Confirm Password">
                                    @error('password_confirmation') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div class="flex flex-col mb-3">
                                    <input type="text" name="referral_code" id="referral_code" class="form-control" placeholder="Kode Referal (Opsional)" aria-label="Referral Code" value="{{ old('referral_code') }}">
                                    @error('referral_code') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                </div>
                                <div id="opd_selection_container" class="flex flex-col mb-3" style="display: none;">
                                    <div class="flex flex-col mb-3">
                                        <label for="opd_id" class="form-label">Pilih Dinas/OPD</label>
                                        <select name="opd_id" id="opd_id" class="form-control select2-searchable">
                                            <option value="">-- Pilih Dinas/OPD --</option>
                                            @foreach($opds as $opd)
                                                <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>
                                                    {{ $opd->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('opd_id') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                    </div>
                                    <div class="flex flex-col mb-3">
                                        <input type="text" name="nip" class="form-control" placeholder="NIP/NIK" aria-label="NIP" value="{{ old('nip') }}">
                                        @error('nip') <p class='text-danger text-xs pt-1'> {{ $message }} </p> @enderror
                                    </div>
                                </div> 
                                <div class="form-check form-check-info text-start">
                                    <input class="form-check-input" type="checkbox" name="terms" id="flexCheckDefault" >
                                    <label class="form-check-label" for="flexCheckDefault">
                                        I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and
                                            Conditions</a>
                                    </label>
                                    @error('terms') <p class='text-danger text-xs'> {{ $message }} </p> @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign up</button>
                                </div>
                                <p class="text-sm mt-3 mb-0">Already have an account? <a href="{{ route('login') }}"
                                        class="text-dark font-weight-bolder">Sign in</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('layouts.footers.guest.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const referralCodeInput = document.getElementById('referral_code');
            const opdSelectionContainer = document.getElementById('opd_selection_container');
            const opdSelect = document.getElementById('opd_id');
            const requiredReferralCode = 'PSSEPSS35';

            function toggleOpdSelection() {
                if (referralCodeInput.value === requiredReferralCode) {
                    opdSelectionContainer.style.display = 'block';
                    opdSelect.setAttribute('required', 'required');
                } else {
                    opdSelectionContainer.style.display = 'none';
                    opdSelect.removeAttribute('required');
                    opdSelect.value = ''; // Reset pilihan OPD jika kode referal tidak sesuai
                }
            }

            // Panggil saat halaman dimuat (untuk kasus old('referral_code'))
            toggleOpdSelection();

            // Panggil saat input kode referal berubah
            referralCodeInput.addEventListener('input', toggleOpdSelection);
        });
    </script>
@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-searchable').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Silakan pilih...'
        });
    });
</script>
@endpush
@endsection

