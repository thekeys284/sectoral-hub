@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

{{-- Tambahkan CSS Select2 --}}
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


@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $user->id ? 'Edit User' : 'Tambah User'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>{{ $user->id ? 'Form Edit User' : 'Form Tambah User' }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ $user->id ? route('master.users.update', $user->id) : route('master.users.store') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($user->id) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">-- Pilih Role --</option>
                                    {{-- Jika yang login adalah ADMIN, tampilkan semua opsi --}}
                                    @if(auth()->user()->role === 'admin')
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="pembina" {{ old('role', $user->role) == 'pembina' ? 'selected' : '' }}>Pembina</option>
                                        <option value="walidata" {{ old('role', $user->role) == 'walidata' ? 'selected' : '' }}>Walidata</option>
                                        <option value="produsen" {{ old('role', $user->role) == 'produsen' ? 'selected' : '' }}>Produsen</option>
                                    
                                    {{-- Jika yang login adalah WALIDATA, tampilkan hanya opsi tertentu --}}
                                    @elseif(auth()->user()->role === 'walidata')
                                        <option value="walidata" {{ old('role', $user->role) == 'walidata' ? 'selected' : '' }}>Walidata</option>
                                        <option value="produsen" {{ old('role', $user->role) == 'produsen' ? 'selected' : '' }}>Produsen</option>
                                    
                                    {{-- Jika role lain (opsional) --}}
                                    @else
                                        <option value="{{ $user->role }}" selected>{{ ucfirst($user->role) }}</option>
                                    @endif
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dinas (OPD)</label>
                                <select name="opd_id" class="form-control select2-searchable">
                                    <option value="">-- Pilih OPD --</option>
                                    @foreach($opds as $opd)
                                        <option value="{{ $opd->id }}" {{ old('opd_id', $user->opd_id) == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password {{ $user->id ? '(Kosongkan jika tidak ganti)' : '' }}</label>
                                <input type="password" name="password" class="form-control" {{ $user->id ? '' : 'required' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No HP</label>
                                {{-- Tambahkan old() agar data lama muncul saat edit --}}
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Foto Profil</label>
                                {{-- Sesuaikan nama 'image' jika di Controller kamu pakai $request->file('image') --}}
                                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                                
                                <div class="mt-3">
                                    {{-- Gunakan accessor getProfilePhotoUrlAttribute jika sudah dibuat di Model --}}
                                    <img id="imagePreview" 
                                        src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : asset('img/placeholder-user.png') }}" 
                                        alt="Preview" 
                                        class="img-thumbnail" 
                                        style="max-height: 150px; display: {{ $user->profile_photo_path ? 'block' : 'block' }};">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ $user->id ? 'Update Data' : 'Simpan User' }}</button>
                        <a href="{{ route('master.users.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('imageInput').onchange = function (evt) {
            const [file] = this.files;
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                preview.style.display = 'block';
                preview.src = URL.createObjectURL(file);
            }
        };
    </script>
@endsection

{{-- Jalankan Script Select2 --}}
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