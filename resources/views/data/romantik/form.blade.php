@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

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
    @include('layouts.navbars.auth.topnav', ['title' => $romantik->id ? (isset($is_show) ? 'Detail Data Romantik' : 'Edit Data Romantik') : 'Tambah Data Romantik'])

    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>{{ $romantik->id ? (isset($is_show) ? 'Detail Romantik' : 'Form Edit Romantik') : 'Form Tambah Romantik' }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ $romantik->id ? route('data.romantik.update', $romantik->id) : route('data.romantik.store') }}" 
                      method="POST">
                    @csrf
                    @if($romantik->id && !isset($is_show)) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Judul Kegiatan</label>
                                <input type="text" name="judul_kegiatan" class="form-control" value="{{ old('judul_kegiatan', $romantik->judul_kegiatan) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dinas (OPD)</label>
                                <select name="opd_id" class="form-control select2-searchable" required @if(isset($is_show)) disabled @endif>
                                    <option value="" disabled selected>-- Pilih OPD --</option>
                                    @foreach($opds as $opd)
                                        <option value="{{ $opd->id }}" {{ old('opd_id', $romantik->opd_id) == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tahun Kegiatan</label>
                                <input type="number" name="tahun_kegiatan" class="form-control" value="{{ old('tahun_kegiatan', $romantik->tahun_kegiatan) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nomor Rekomendasi</label>
                                <input type="text" name="nomor_rekomendasi" class="form-control" value="{{ old('nomor_rekomendasi', $romantik->nomor_rekomendasi) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Pengajuan</label>
                                <input type="date" name="tgl_pengajuan" class="form-control" value="{{ old('tgl_pengajuan', $romantik->tgl_pengajuan ? date('Y-m-d', strtotime($romantik->tgl_pengajuan)) : date('Y-m-d')) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Perbaikan Terakhir</label>
                                <input type="date" name="tgl_perbaikan_terakhir" class="form-control" value="{{ old('tgl_perbaikan_terakhir', $romantik->tgl_perbaikan_terakhir ? date('Y-m-d', strtotime($romantik->tgl_perbaikan_terakhir)) : date('Y-m-d')) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" class="form-control" value="{{ old('tgl_selesai', $romantik->tgl_selesai ? date('Y-m-d', strtotime($romantik->tgl_selesai)) : date('Y-m-d')) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Lama Pemeriksaan</label>
                                <input type="number" name="lama_pemeriksaan" class="form-control" value="{{ old('lama_pemeriksaan', $romantik->lama_pemeriksaan) }}" required @if(isset($is_show)) readonly @endif>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status Pengajuan</label>
                                <select name="status_pengajuan" class="form-control" required @if(isset($is_show)) disabled @endif>
                                    <option value="" disabled {{ old('status_pengajuan', $romantik->status_pengajuan) == '' ? 'selected' : '' }}>
                                        -- Pilih Status --
                                    </option>
                                    <option value="pemeriksaan" {{ old('status_pengajuan', $romantik->status_pengajuan) == 'pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                                    <option value="penerbitan" {{ old('status_pengajuan', $romantik->status_pengajuan) == 'penerbitan' ? 'selected' : '' }}>Penerbitan</option>
                                    <option value="pengesahan" {{ old('status_pengajuan', $romantik->status_pengajuan) == 'pengesahan' ? 'selected' : '' }}>Pengesahan</option>
                                    <option value="perbaikan" {{ old('status_pengajuan', $romantik->status_pengajuan) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>                                                                    
                                    <option value="selesai" {{ old('status_pengajuan', $romantik->status_pengajuan) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status Rekomendasi</label>
                                <select name="status_rekomendasi" class="form-control" required @if(isset($is_show)) disabled @endif>
                                    <option value="" disabled {{ old('status_rekomendasi', $romantik->status_rekomendasi) == '' ? 'selected' : '' }}>
                                        -- Pilih Status --
                                    </option>
                                    <option value="dibatalkan" {{ old('status_rekomendasi', $romantik->status_rekomendasi) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    <option value="ditolak" {{ old('status_rekomendasi', $romantik->status_rekomendasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="layak" {{ old('status_rekomendasi', $romantik->status_rekomendasi) == 'layak' ? 'selected' : '' }}>Layak</option>
                                </select>
                            </div>
                        </div>
                    </div> 

                    <div class="mt-4">
                        @if(!isset($is_show))
                            <button type="submit" class="btn btn-primary">{{ $romantik->id ? 'Update Romantik' : 'Simpan Romantik' }}</button>
                        @endif
                        <a href="{{ route('data.romantik.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-searchable').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush