@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Monitoring Satu Data OPD'])
    <div class="container-fluid py-4">
        
        {{-- Card Tabel Rekapitulasi --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Tabel Monitoring Identifikasi Satu Data Per OPD</h6>
                            <p class="text-sm mb-0">Kompilasi Daftar Data, Kegiatan, Standar Data, Romantik, dan Metadata</p>
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
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">SDSN</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Metadata</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Romantik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rekap_opd as $opd)
                                    <tr>
                                        <td class="text-center text-sm">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm text-wrap">{{ $opd->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                        @if($opd->daftardata_count > 0)
                                            <a href="javascript:;" class="text-sm font-weight-bold text-decoration-underline btn-detail" data-id="{{ $opd->id }}" data-type="daftardata">{{ $opd->daftardata_count }}</a>
                                        @else
                                            <span class="text-sm font-weight-bold">{{ $opd->daftardata_count }}</span>
                                        @endif
                                        </td>
                                        <td class="align-middle text-center">
                                        @if($opd->kegiatan_count > 0)
                                            <a href="javascript:;" class="text-sm font-weight-bold text-info text-decoration-underline btn-detail" data-id="{{ $opd->id }}" data-type="kegiatan">{{ $opd->kegiatan_count }}</a>
                                        @else
                                            <span class="text-sm font-weight-bold text-info">{{ $opd->kegiatan_count }}</span>
                                        @endif
                                        </td>
                                        <td class="align-middle text-center">
                                        @if($opd->sdsn_count > 0)
                                            <a href="javascript:;" class="text-sm font-weight-bold text-success text-decoration-underline btn-detail" data-id="{{ $opd->id }}" data-type="sdsn">{{ $opd->sdsn_count }}</a>
                                        @else
                                            <span class="text-sm font-weight-bold text-success">{{ $opd->sdsn_count }}</span>
                                        @endif
                                        </td>
                                        <td class="align-middle text-center">
                                        @if($opd->metadata_count > 0)
                                            <a href="javascript:;" class="text-sm font-weight-bold text-primary text-decoration-underline btn-detail" data-id="{{ $opd->id }}" data-type="metadata">{{ $opd->metadata_count }}</a>
                                        @else
                                            <span class="text-sm font-weight-bold text-primary">{{ $opd->metadata_count }}</span>
                                        @endif
                                        </td>
                                        <td class="align-middle text-center">
                                        @if($opd->romantik_count > 0)
                                            <a href="javascript:;" class="text-sm font-weight-bold text-warning text-decoration-underline btn-detail" data-id="{{ $opd->id }}" data-type="romantik">{{ $opd->romantik_count }}</a>
                                        @else
                                            <span class="text-sm font-weight-bold text-warning">{{ $opd->romantik_count }}</span>
                                        @endif
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
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('sdsn_count') }}</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('metadata_count') }}</td>
                                        <td class="text-center text-sm">{{ $rekap_opd->sum('romantik_count') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- Modal Detail Data --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailTitle">Detail Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="loadingIndicator" class="text-center d-none py-3">
                        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                    <ul class="list-group list-group-flush" id="modalDetailList"></ul>
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

    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        let opdId = $(this).data('id');
        let type = $(this).data('type');
        
        $('#modalDetailTitle').text('Memuat Data...');
        $('#modalDetailList').empty();
        $('#loadingIndicator').removeClass('d-none');
        $('#modalDetail').modal('show');

        $.ajax({
            url: `/monitoring/detail/${opdId}/${type}`,
            method: 'GET',
            success: function(res) {
                $('#loadingIndicator').addClass('d-none');
                $('#modalDetailTitle').text(res.title);
                if (res.data && res.data.length > 0) {
                    res.data.forEach((item, idx) => {
                        $('#modalDetailList').append(`<li class="list-group-item text-sm">${idx + 1}. ${item}</li>`);
                    });
                } else {
                    $('#modalDetailList').append(`<li class="list-group-item text-sm text-center text-muted">Tidak ada data</li>`);
                }
            },
            error: function() {
                $('#loadingIndicator').addClass('d-none');
                $('#modalDetailTitle').text('Terjadi Kesalahan');
                $('#modalDetailList').append(`<li class="list-group-item text-sm text-center text-danger">Gagal memuat data.</li>`);
            }
        });
    });
    });
</script>
@endpush