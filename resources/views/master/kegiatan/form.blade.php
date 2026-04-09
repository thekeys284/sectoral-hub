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
    @include('layouts.navbars.auth.topnav', ['title' => $kegiatan->id ? 'Edit Kegiatan' : 'Tambah Kegiatan'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>{{ $kegiatan->id ? 'Form Edit Kegiatan' : 'Form Tambah Kegiatan' }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ $kegiatan->id ? route('admin.kegiatan.update', $kegiatan->id) : route('admin.kegiatan.store') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($kegiatan->id) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Kegiatan</label>
                                <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Periode Kegiatan</label>
                                <select name="periode_kegiatan" class="form-control" required>
                                    <option value="" disabled {{ old('periode_kegiatan', $kegiatan->periode_kegiatan) == '' ? 'selected' : '' }}>
                                        -- Pilih Periode Kegiatan --
                                    </option>
                                    <option value="bulanan" {{ old('periode_kegiatan', $kegiatan->periode_kegiatan) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="triwulanan" {{ old('periode_kegiatan', $kegiatan->periode_kegiatan) == 'triwulanan' ? 'selected' : '' }}>Triwulanan</option>
                                    <option value="tahunan" {{ old('periode_kegiatan', $kegiatan->periode_kegiatan) == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun Kegiatan</label>
                                <input type="number" name="tahun_kegiatan" class="form-control" value="{{ old('tahun_kegiatan', $kegiatan->tahun_kegiatan ?? date('Y')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cara Pengumpulan Data</label>
                                <select name="cara_pengumpulan_data" class="form-control">
                                    <option value="" disabled {{ old('cara_pengumpulan_data', $kegiatan->cara_pengumpulan_data) == '' ? 'selected' : '' }}>
                                        -- Pilih Cara Pengumpulan Data --
                                    </option>
                                    <option value="sensus" {{ old('cara_pengumpulan_data', $kegiatan->cara_pengumpulan_data) == 'sensus' ? 'selected' : '' }}>Sensus</option>
                                    <option value="survei" {{ old('cara_pengumpulan_data', $kegiatan->cara_pengumpulan_data) == 'survei' ? 'selected' : '' }}>Survei</option>
                                    <option value="kompromin" {{ old('cara_pengumpulan_data', $kegiatan->cara_pengumpulan_data) == 'kompromin' ? 'selected' : '' }}>Kompilasi Produk Administrasi</option>
                                    <option value="cara_lain" {{ old('cara_pengumpulan_data', $kegiatan->cara_pengumpulan_data) == 'cara_lain' ? 'selected' : '' }}>Cara lain</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dinas (OPD)</label>
                                <select name="opd_id" class="form-control select2-searchable">
                                    <option value="">-- Pilih OPD --</option>
                                    @foreach($opds as $opd)
                                        <option value="{{ $opd->id }}" {{ old('opd_id', $kegiatan->opd_id) == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Metadata</label>
                                <select name="metadata_id" class="form-control select2-searchable">
                                    <option value="">-- Pilih Metadata --</option>
                                    @foreach($metadatas as $metadata)
                                        <option value="{{ $metadata->id }}" {{ old('metadata_id', $kegiatan->metadata_id) == $metadata->id ? 'selected' : '' }}>
                                        {{ $metadata->judul_kegiatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Romantik</label>
                                <select name="romantik_id" class="form-control select2-searchable">
                                    <option value="">-- Pilih Romantik --</option>
                                    @foreach($romantiks as $romantik)
                                        <option value="{{ $romantik->id }}" {{ old('romantik_id', $kegiatan->romantik_id) == $romantik->id ? 'selected' : '' }}>
                                        {{ $romantik->judul_kegiatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Utama</label>
                                <textarea name="data_utama" class="form-control" rows="3">{{ old('data_utama', $kegiatan->data_utama) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Prioritas</label>
                                <select name="data_prioritas" class="form-control">
                                    <option value="" disabled {{ old('data_prioritas', $kegiatan->data_prioritas) == '' ? 'selected' : '' }}>
                                        -- Apakah masuk Data Prioritas? --
                                    </option>
                                    <option value="ya" {{ old('data_prioritas', $kegiatan->data_prioritas) == 'ya' ? 'selected' : '' }}>Ya</option>
                                    <option value="tidak" {{ old('data_prioritas', $kegiatan->data_prioritas) == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                </select>                            
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Aksesbilitas</label>
                                <select name="aksesbilitas" class="form-control">
                                    <option value="" disabled {{ old('aksesbilitas', $kegiatan->aksesbilitas) == '' ? 'selected' : '' }}>
                                        -- Bagaimana pemanfaatan data tersebut? --
                                    </option>
                                    <option value="internal" {{ old('aksesbilitas', $kegiatan->aksesbilitas) == 'internal' ? 'selected' : '' }}>Khusus Internal</option>
                                    <option value="terbatas" {{ old('aksesbilitas', $kegiatan->aksesbilitas) == 'terbatas' ? 'selected' : '' }}>Terbatas</option>
                                    <option value="publik" {{ old('aksesblitas', $kegiatan->aksesbilitas) == 'publik' ? 'selected' : '' }}>Terbuka (Publik)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ $kegiatan->id ? 'Update Data' : 'Simpan Kegiatan' }}</button>
                        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
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