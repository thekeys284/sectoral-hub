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
    @include('layouts.navbars.auth.topnav', ['title' => $sdsn->id ? 'Edit SDSN' : 'Tambah SDSN'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>{{ $sdsn->id ? 'Form Edit SDSN' : 'Form Tambah SDSN' }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ $sdsn->id ? route('master.sdsn.update', $sdsn->id) : route('master.sdsn.store') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($sdsn->id) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode SDSN</label>
                                <input type="text" name="kode_sdsn" class="form-control" value="{{ old('kode_sdsn', $sdsn->kode_sdsn) }}" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Data</label>
                                <input type="text" name="nama_data" class="form-control" value="{{ old('nama_data', $sdsn->nama_data) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Konsep</label>
                                <textarea name="konsep" class="form-control" rows="3" required>{{ old('konsep', $sdsn->konsep) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Definisi</label>
                                <textarea name="definisi" class="form-control" rows="3" required>{{ old('definisi', $sdsn->definisi) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Klasifikasi Penyajian</label>
                                <input type="text" name="klasifikasi_penyajian" class="form-control" value="{{ old('klasifikasi_penyajian', $sdsn->klasifikasi_penyajian) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ukuran</label>
                                <input type="text" name="ukuran" class="form-control" value="{{ old('ukuran', $sdsn->ukuran) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Satuan</label>
                                <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $sdsn->satuan) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tahun Penetapan</label>
                                <input type="date" name="tahun_penetapan" class="form-control" value="{{ old('tahun_penetapan', $sdsn->tahun_penetapan) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ $sdsn->id ? 'Update Data' : 'Simpan SDSN' }}</button>
                        <a href="{{ route('master.sdsn.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
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