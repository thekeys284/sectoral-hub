@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Rekapitulasi Capaian OPD'])
    <div class="container-fluid py-4">
        
        {{-- Card Tabel Rekapitulasi --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Tabel Capaian Data Sektoral Per OPD</h6>
                            <p class="text-sm mb-0">Kompilasi Daftar Data, Kegiatan, Romantik, dan Metadata</p>
                        </div>
                        <button class="btn btn-sm bg-gradient-success">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="rekap-table">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama OPD</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Daftar Data</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kegiatan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Romantik</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Metadata Keg</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Metadata Var</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rekap_opd as $opd)
                                    <tr>
                                        <td class="text-center text-sm">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $opd->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold">{{ $opd->daftardata_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold text-info">{{ $opd->kegiatan_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold text-warning">{{ $opd->romantik_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold">{{ $opd->metadata_keg_count ?? 0 }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-{{ $opd->metadata_var_count > 0 ? 'success' : 'secondary' }}">
                                                {{ $opd->metadata_var_count ?? 0 }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                {{-- Footer Total (Sesuai Gambar) --}}
                                <tfoot class="bg-gray-100 font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-center text-sm font-weight-bolder">Total</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('daftardata_count') }}</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('kegiatan_count') }}</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('romantik_count') }}</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('metadata_keg_count') }}</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('metadata_var_count') }}</td>
                                    </tr>
                                </tfoot>
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
        $('#rekap-table').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari OPD:",
                "paginate": { "previous": "<", "next": ">" }
            }
        });
    });
</script>
@endpush