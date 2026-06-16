@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])
    <div class="container-fluid py-4">
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Dashboard</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="monitoring-table">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kegiatan & OPD</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cara Pengumpulan Data</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status Metadata</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status Romantik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monitoringData as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm text-wrap">{{ $item->nama_kegiatan }}</h6>
                                                    <p class="text-xs text-primary font-weight-bold mb-0">
                                                        <i class="ni ni-building me-1"></i>{{ $item->opd->name ?? 'Dinas Tidak Diketahui' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                            @php
                                                $statusMapping = [
                                                    'sensus' => ['label' => 'Sensus', 'class' => 'bg-gradient-success'],
                                                    'survei' => ['label' => 'Survei', 'class' => 'bg-gradient-info'],
                                                    'kompromin' => ['label' => 'Kompilasi Produk Administrasi', 'class' => 'bg-gradient-warning'],
                                                    'cara_lain' => ['label' => 'Cara Lain', 'class' => 'bg-gradient-secondary'],
                                                ];

                                                $statusData = $statusMapping[$item->cara_pengumpulan_data] ?? [
                                                    'label' => ucfirst($item->cara_pengumpulan_data),
                                                    'class' => 'bg-gradient-secondary'
                                                ];
                                            @endphp

                                        <td class="align-middle text-center text-sm text-wrap">
                                            <span class="badge badge-sm {{ $statusData['class'] }}">
                                                {{ $statusData['label'] }}
                                            </span>
                                            <p class="text-xs text-secondary mb-0">{{ ucfirst($item->periode_kegiatan) }} - {{ $item->tahun_kegiatan }}</p>
                                        </td>

                                        <td>
                                            @if($item->metadata)
                                                <p class="text-xs font-weight-bold mb-0 text-wrap">{{ Str::limit($item->metadata->judul_kegiatan, 30) }}</p>
                                                <span class="badge badge-sm bg-gradient-{{ $item->metadata->status == 'approved' ? 'success' : 'secondary' }}">
                                                    {{ $item->metadata->status }}
                                                </span>
                                                <p class="text-xxs text-secondary mt-1">Periode: {{ $item->metadata->periode_submission }}</p>
                                            @else
                                                <span class="text-xs text-muted">Belum lapor metadata</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($item->romantik)
                                                <p class="text-xs font-weight-bold mb-0 text-wrap">No: {{ $item->romantik->nomor_rekomendasi }}</p>
                                                <div class="d-flex flex-column">
                                                    <span class="text-xxs">Pengajuan: {{ $item->romantik->status_pengajuan }}</span>
                                                    <span class="text-xxs font-weight-bold text-{{ $item->romantik->status_rekomendasi == 'layak' ? 'success' : 'danger' }}">
                                                        Hasil: {{ $item->romantik->status_rekomendasi }}
                                                    </span>
                                                    <p class="text-xxs text-secondary mb-0">Tgl: {{ date('d/m/y', strtotime($item->romantik->tgl_pengajuan)) }}</p>
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