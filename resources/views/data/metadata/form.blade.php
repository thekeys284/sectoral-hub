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
    @include('layouts.navbars.auth.topnav', ['title' => $metadata->id ? 'Edit Metadata' : 'Tambah Metadata'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>{{ $metadata->id ? 'Form Edit Metadata' : 'Form Tambah Metadata' }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ $metadata->id ? route('data.metadata.update', $metadata->id) : route('data.metadata.store') }}" 
                      method="POST">
                    @csrf
                    @if($metadata->id) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Judul Kegiatan</label>
                                <input type="text" name="judul_kegiatan" class="form-control" value="{{ old('judul_kegiatan', $metadata->judul_kegiatan) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Periode Submission (Tahun)</label>
                                <input type="number" name="periode_submission" class="form-control" value="{{ old('periode_submission', $metadata->periode_submission ?? date('Y')) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Submission</label>
                                {{-- FIX: Format date harus Y-m-d agar terbaca browser --}}
                                <input type="date" name="tanggal_submission" class="form-control" value="{{ old('tanggal_submission', $metadata->tanggal_submission ? date('Y-m-d', strtotime($metadata->tanggal_submission)) : date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dinas (OPD)</label>
                                <select name="opd_id" class="form-control select2-searchable" required>
                                    <option value="">-- Pilih OPD --</option>
                                    @foreach($opds as $opd)
                                        {{-- FIX: Langsung ambil opd_id dari metadata --}}
                                        <option value="{{ $opd->id }}" {{ old('opd_id', $metadata->opd_id) == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status Pengajuan</label>
                                <select name="status" class="form-control" required>
                                    <option value="" disabled {{ old('status', $metadata->status) == '' ? 'selected' : '' }}>
                                        -- Pilih Status --
                                    </option>
                                    <option value="submitted" {{ old('status', $metadata->status) == 'submitted' ? 'selected' : '' }}>Disubmit</option>
                                    <option value="approved" {{ old('status', $metadata->status) == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="revised" {{ old('status', $metadata->status) == 'revised' ? 'selected' : '' }}>Telah Direvisi</option>
                                    <option value="rejected" {{ old('status', $metadata->status) == 'rejected' ? 'selected' : '' }}>Ditolak</option>                                                                    
                                    <option value="correction_required" {{ old('status', $metadata->status) == 'correction_required' ? 'selected' : '' }}>Perlu Perbaikan</option>
                                </select>
                            </div>
                        </div>
                    </div> {{-- Penutup Row --}}

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ $metadata->id ? 'Update Metadata' : 'Simpan Metadata' }}
                        </button>
                        <a href="{{ route('data.metadata.index') }}" class="btn btn-secondary">Batal</a>
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