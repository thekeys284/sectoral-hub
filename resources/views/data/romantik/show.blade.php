@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detail Data Romantik'])
    <div class="container-fluid py-4">
        <div class="card shadow-lg">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6>Detail Rekomendasi Statistik (Romantik)</h6>
                <span class="badge bg-gradient-info">Informasi Detail</span>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Judul Kegiatan Full Width --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Judul Kegiatan</label>
                            <p class="form-control bg-gray-100">{{ $romantik->judul_kegiatan }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Tahun Kegiatan</label>
                            <p class="form-control bg-gray-100">{{ $romantik->tahun_kegiatan }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Nomor Rekomendasi</label>
                            <p class="form-control bg-gray-100">{{ $romantik->nomor_rekomendasi ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Tanggal Pengajuan</label>
                            <p class="form-control bg-gray-100">{{ $romantik->tgl_pengajuan ? date('d F Y', strtotime($romantik->tgl_pengajuan)) : '-' }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Perbaikan Terakhir</label>
                            <p class="form-control bg-gray-100">{{ $romantik->tgl_perbaikan_terakhir ? date('d F Y', strtotime($romantik->tgl_perbaikan_terakhir)) : '-' }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Tanggal Selesai</label>
                            <p class="form-control bg-gray-100">{{ $romantik->tgl_selesai ? date('d F Y', strtotime($romantik->tgl_selesai)) : '-' }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Lama Pemeriksaan (Hari)</label>
                            <p class="form-control bg-gray-100">{{ $romantik->lama_pemeriksaan }} Hari</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Status Pengajuan</label>
                            <div>
                                <span class="badge bg-gradient-{{ $romantik->status_pengajuan == 'selesai' ? 'success' : 'primary' }}">
                                    {{ strtoupper($romantik->status_pengajuan) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">Status Rekomendasi</label>
                            <div>
                                <span class="badge bg-gradient-{{ $romantik->status_rekomendasi == 'layak' ? 'success' : 'danger' }}">
                                    {{ strtoupper($romantik->status_rekomendasi) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div> 

                <div class="mt-4 border-top pt-3">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    @if(auth()->user()->role == 'admin')
                        <a href="{{ route('admin.romantik.edit', $romantik->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Data
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection