@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Daftar Data'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Manajemen Daftar Data</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="daftar-data-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Data</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Satuan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Periode</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sifat</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($daftardata as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm text-wrap">{{ $item->nama_data }}</h6>
                                                    <p class="text-xs text-secondary mb-0 text-wrap">Dinas: {{ $item->opd->name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle"><span class="text-xs font-weight-bold text-wrap">{{ $item->satuan }}</span></td>
                                        <td class="align-middle text-center"><span class="text-xs">{{ ucfirst($item->periode) }}</span></td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm 
                                                {{ $item->sifat_data == 'Terbuka' ? 'bg-gradient-success' : 
                                                ($item->sifat_data == 'Terbatas' ? 'bg-gradient-warning' : 
                                                'bg-gradient-secondary') }}">
                                                
                                                {{ ucfirst($item->sifat_data) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <button 
                                                class="btn btn-info btn-sm mb-0 btn-show"
                                                data-nama="{{ $item->nama_data }}"
                                                data-satuan="{{ $item->satuan }}"
                                                data-opd="{{ $item->opd->name ?? '-' }}"
                                                data-periode="{{ ucfirst($item->periode) }}"
                                                data-kedalaman="{{ $item->kedalaman_kabkot }}"
                                                data-sifat="{{ ucfirst($item->sifat_data) }}"
                                                data-sumber="{{ $item->sumber_data }}"
                                                data-kegiatan="{{ $item->kegiatan->nama_kegiatan ?? '-' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalShow">                                    
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalShow" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Daftar Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Nama Data</label>
                                <p id="show_nama_data" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Satuan</label>
                                <p id="show_satuan" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">OPD Pemilik Data</label>
                                <p id="show_opd" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Periode</label>
                                <p id="show_periode" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Kedalaman Kab/Kot?</label>
                                <p id="show_kedalaman" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Sifat Data</label>
                                <p id="show_sifat" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold">Sumber Data</label>
                                <p id="show_sumber" class="form-control bg-gray-100"></p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold">Kegiatan Statistik Terkait</label>
                                <p id="show_kegiatan" class="form-control bg-gray-100"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
            </div>
        </div>
    </div>

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#daftar-data-table').DataTable({
            paging: true,
            searching: true,
            info: true
        });

        // Menangani klik tombol detail untuk mengisi data ke dalam modal
        $(document).on('click', '.btn-show', function() {
            $('#show_nama_data').text($(this).data('nama'));
            $('#show_satuan').text($(this).data('satuan'));
            $('#show_opd').text($(this).data('opd'));
            $('#show_periode').text($(this).data('periode'));
            $('#show_kedalaman').text($(this).data('kedalaman'));
            $('#show_sifat').text($(this).data('sifat'));
            $('#show_sumber').text($(this).data('sumber'));
            $('#show_kegiatan').text($(this).data('kegiatan'));
        });
    });
</script>
@endpush
@endsection