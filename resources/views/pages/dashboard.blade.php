@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Monitoring Hub'])
    <div class="container-fluid py-4">
        
        {{-- Tabel Monitoring Gabungan --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Tabel Monitoring Hub (Integrasi Data)</h6>
                        <span class="badge bg-gradient-primary">Real-time Data</span>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="monitoring-table">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kegiatan & OPD</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detail Kegiatan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status Metadata</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status Romantik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monitoringData as $data)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $data->nama_kegiatan }}</h6>
                                                    <p class="text-xs text-primary font-weight-bold mb-0">
                                                        <i class="ni ni-building me-1"></i>{{ $data->opd->name ?? 'Dinas Tidak Diketahui' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ ucfirst($data->cara_pengumpulan_data) }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ ucfirst($data->periode_kegiatan) }} - {{ $data->tahun_kegiatan }}</p>
                                        </td>

                                        <td>
                                            @if($data->metadata)
                                                <p class="text-xs font-weight-bold mb-0">{{ Str::limit($data->metadata->judul_kegiatan, 30) }}</p>
                                                <span class="badge badge-sm bg-gradient-{{ $data->metadata->status == 'approved' ? 'success' : 'secondary' }}">
                                                    {{ $data->metadata->status }}
                                                </span>
                                                <p class="text-xxs text-secondary mt-1">Periode: {{ $data->metadata->periode_submission }}</p>
                                            @else
                                                <span class="text-xs text-muted">Belum lapor metadata</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($data->romantik)
                                                <p class="text-xs font-weight-bold mb-0">No: {{ $data->romantik->nomor_rekomendasi }}</p>
                                                <div class="d-flex flex-column">
                                                    <span class="text-xxs">Pengajuan: {{ $data->romantik->status_pengajuan }}</span>
                                                    <span class="text-xxs font-weight-bold text-{{ $data->romantik->status_rekomendasi == 'layak' ? 'success' : 'danger' }}">
                                                        Hasil: {{ $data->romantik->status_rekomendasi }}
                                                    </span>
                                                    <p class="text-xxs text-secondary mb-0">Tgl: {{ date('d/m/y', strtotime($data->romantik->tgl_pengajuan)) }}</p>
                                                </div>
                                            @else
                                                <span class="text-xs text-muted italic">Tidak ada rekomendasi</span>
                                            @endif
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

        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#monitoring-table').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari Data Gabungan:",
                "paginate": { "previous": "<", "next": ">" }
            }
        });
    });
</script>
@endpush