@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detail Metadata'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0 d-flex justify-content-between">
                <h6>Detail Metadata: {{ $metadata->judul_kegiatan }}</h6>
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label font-weight-bold">Judul Kegiatan</label>
                        <p class="form-control bg-gray-100">{{ $metadata->judul_kegiatan }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">Periode Submission</label>
                        <p class="form-control bg-gray-100">{{ $metadata->periode_submission }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">Tanggal Submission</label>
                        <p class="form-control bg-gray-100">{{ date('d M Y', strtotime($metadata->tanggal_submission)) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">Dinas (OPD)</label>
                        <p class="form-control bg-gray-100">{{ $metadata->opd->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Status Pengajuan</label>
                        <br>
                        <span class="badge bg-gradient-success">{{ strtoupper($metadata->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection