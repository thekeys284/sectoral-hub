@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Rekapitulasi Telaah Pelaporan Metadata'])

    <div class="container-fluid py-4">
        {{-- RINGKASAN MONITORING CARDS --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-xs mb-0 text-uppercase font-weight-bolder text-danger">Belum Diperiksa (Pending)</p>
                                    <h4 class="font-weight-bolder text-danger mb-0">
                                        {{ $summary['total_belum_ditelaah'] }} <span class="text-xs font-weight-normal text-secondary">Dokumen</span>
                                    </h4>
                                    <small class="text-xxs text-secondary">
                                        Tersebar di <strong>{{ $summary['opd_belum_diperiksa'] }} Dinas</strong> (Menunggu Feedback)
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="fas fa-exclamation-circle text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-xs mb-0 text-uppercase font-weight-bolder text-warning">Sudah Ditelaah: Revisi</p>
                                    <h4 class="font-weight-bolder text-dark mb-0">
                                        {{ $summary['total_butuh_perbaikan'] }} <span class="text-xs font-weight-normal text-secondary">Dokumen</span>
                                    </h4>
                                    <small class="text-xxs text-secondary">
                                        Sudah diberi feedback (<strong>Perlu Perbaikan</strong>)
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-comment-dots text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-xs mb-0 text-uppercase font-weight-bolder text-success">Sudah Ditelaah: Disetujui</p>
                                    <h4 class="font-weight-bolder text-success mb-0">
                                        {{ $summary['total_disetujui'] }} <span class="text-xs font-weight-normal text-secondary">Dokumen</span>
                                    </h4>
                                    <small class="text-xxs text-secondary">
                                        Metadata telah diperiksa &amp; <strong>Disetujui</strong>
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-check-double text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-4 border-secondary h-100">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-xs mb-0 text-uppercase font-weight-bolder text-secondary">Belum Upload Link</p>
                                    <h4 class="font-weight-bolder text-dark mb-0">
                                        {{ $summary['total_belum_upload'] }} <span class="text-xs font-weight-normal text-secondary">/ {{ $summary['total_target_metadata'] }}</span>
                                    </h4>
                                    <small class="text-xxs text-secondary">
                                        Dari total <strong>{{ $summary['total_kegiatan'] }} Kegiatan</strong> ({{ $summary['total_opd'] }} OPD)
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-secondary shadow-secondary text-center rounded-circle">
                                    <i class="fas fa-folder-open text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD UTAMA DENGAN NAV TABS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header pb-2 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
                <div>
                    <h6 class="font-weight-bolder text-dark mb-0">
                        <i class="fas fa-clipboard-check text-primary me-2"></i>Monitoring Pemeriksaan &amp; Status Terakhir Pelaporan Metadata
                    </h6>
                    <p class="text-xs text-secondary mb-0">
                        Pantau dinas/OPD mana yang sudah mengunggah metadata namun <strong>belum dilakukan pemeriksaan/pemberian feedback</strong> oleh Pembina, beserta status telaah terakhirnya.
                    </p>
                </div>

                <ul class="nav nav-pills nav-fill p-1 bg-gray-100 rounded-3" id="rekapTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-xs font-weight-bold py-2 px-3 mb-0" id="tab-opd-btn" data-bs-toggle="tab" data-bs-target="#pane-opd" type="button" role="tab">
                            <i class="fas fa-building me-1"></i> Rekap Per Dinas / OPD
                            @if($summary['opd_belum_diperiksa'] > 0)
                                <span class="badge bg-danger text-white ms-1">{{ $summary['opd_belum_diperiksa'] }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-xs font-weight-bold py-2 px-3 mb-0" id="tab-kegiatan-btn" data-bs-toggle="tab" data-bs-target="#pane-kegiatan" type="button" role="tab">
                            <i class="fas fa-list-check me-1"></i> Detail Per Kegiatan (MS-Keg, MS-Var, MS-Ind)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body px-3 pt-3 pb-4">
                <div class="tab-content" id="rekapTabContent">

                    {{-- ========================================================= --}}
                    {{-- TAB 1: REKAPITULASI MONITORING PER DINAS / OPD            --}}
                    {{-- ========================================================= --}}
                    <div class="tab-pane fade show active" id="pane-opd" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 bg-gray-100 p-3 rounded-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <label class="text-xs font-weight-bolder text-dark mb-0 me-1">
                                    <i class="fas fa-filter text-primary me-1"></i>Filter Status Pemeriksaan Dinas:
                                </label>
                                <select id="filter-status-opd" class="form-select form-select-sm text-xs font-weight-bold" style="width: 280px;">
                                    <option value="">-- Tampilkan Semua Dinas ({{ count($rekapOpd) }}) --</option>
                                    <option value="Ada Metadata Belum Diperiksa">⚠️ Belum Diperiksa / Menunggu Feedback Pembina</option>
                                    <option value="Sudah Ditelaah — Perlu Perbaikan">🔧 Sudah Ditelaah — Perlu Perbaikan</option>
                                    <option value="Selesai (Disetujui Semua)">✅ Selesai (Disetujui Semua)</option>
                                    <option value="Sudah Ditelaah (Sebagian Belum Upload)">ℹ️ Sudah Ditelaah (Sebagian Belum Upload)</option>
                                    <option value="Dinas Belum Upload Link">⏳ Dinas Belum Upload Link Metadata</option>
                                    <option value="Belum Ada Kegiatan Terhubung">⚪ Belum Ada Kegiatan Terhubung</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0 table-hover table-bordered" id="table-rekap-opd">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 40px;">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Dinas / Perangkat Daerah</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Pembina BPS</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jml Kegiatan<br><span class="text-xxs font-weight-normal">(Target Dokumen)</span></th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sudah Upload<br>Link</th>
                                        <th class="text-center text-uppercase text-danger text-xxs font-weight-bolder bg-danger-subtle">Belum Diperiksa /<br>Menunggu Feedback</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sudah Ditelaah<br><span class="text-xxs font-weight-normal">(Revisi / Disetujui)</span></th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status Terakhir Pemeriksaan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapOpd as $idx => $row)
                                        <tr class="{{ $row['belum_ditelaah'] > 0 ? 'table-warning' : '' }}">
                                            <td class="text-center text-xs font-weight-bold">{{ $idx + 1 }}</td>
                                            <td>
                                                <h6 class="mb-0 text-xs font-weight-bolder text-dark text-wrap">{{ $row['opd']->name }}</h6>
                                                @if($row['latest_activity_at'])
                                                    <small class="text-xxs text-secondary">
                                                        <i class="far fa-clock me-1"></i>Update terakhir: {{ $row['latest_activity_at']->format('d M Y, H:i') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($row['pembina'])
                                                    <span class="text-xs font-weight-bold text-dark d-block">{{ $row['pembina']->name }}</span>
                                                    <small class="text-xxs text-secondary">{{ $row['pembina']->email }}</small>
                                                @else
                                                    <span class="badge badge-xs bg-light text-secondary border">Belum Ditentukan</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="text-xs font-weight-bolder text-dark">{{ $row['total_kegiatan'] }} Kegiatan</span>
                                                <small class="d-block text-xxs text-secondary">({{ $row['total_target'] }} Metadata)</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-sm {{ $row['sudah_upload'] > 0 ? 'bg-gradient-info' : 'bg-light text-secondary border' }}">
                                                    {{ $row['sudah_upload'] }} / {{ $row['total_target'] }}
                                                </span>
                                                @if($row['belum_upload'] > 0)
                                                    <small class="d-block text-xxs text-muted mt-1">{{ $row['belum_upload'] }} belum upload</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($row['belum_ditelaah'] > 0)
                                                    <span class="badge badge-sm bg-gradient-danger px-3 py-2">
                                                        <i class="fas fa-bell me-1"></i> {{ $row['belum_ditelaah'] }} Belum Ditelaah
                                                    </span>
                                                @elseif($row['sudah_upload'] > 0)
                                                    <span class="badge badge-sm bg-success-subtle text-success border border-success-subtle">
                                                        <i class="fas fa-check me-1"></i> 0 (Sudah Diperiksa)
                                                    </span>
                                                @else
                                                    <span class="text-xs text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($row['sudah_ditelaah'] > 0)
                                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                        <span class="badge badge-xs bg-warning text-dark" title="Sudah ditelaah - Perlu Perbaikan">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Revisi: {{ $row['butuh_perbaikan'] }}
                                                        </span>
                                                        <span class="badge badge-xs bg-success" title="Sudah ditelaah - Disetujui">
                                                            <i class="fas fa-check-circle me-1"></i>Disetujui: {{ $row['disetujui'] }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-xxs text-secondary">Belum ada telaah</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="d-none">{{ $row['belum_ditelaah'] > 0 ? 'Ada Metadata Belum Diperiksa' : $row['status_text'] }}</span>
                                                <span class="badge badge-xs {{ $row['status_badge'] }} text-wrap">
                                                    {{ $row['status_text'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('pelaporan.metadata.index', ['opd_id' => $row['opd']->id]) }}" 
                                                   class="btn btn-xs {{ $row['belum_ditelaah'] > 0 ? 'bg-gradient-danger' : 'bg-gradient-primary' }} mb-0">
                                                    <i class="fas fa-external-link-alt me-1"></i> {{ $row['belum_ditelaah'] > 0 ? 'Periksa Sekarang' : 'Lihat Detail' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- TAB 2: DETAIL STATUS PER KEGIATAN (MS-KEG, MS-VAR, MS-IND)--}}
                    {{-- ========================================================= --}}
                    <div class="tab-pane fade" id="pane-kegiatan" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 bg-gray-100 p-3 rounded-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <label class="text-xs font-weight-bolder text-dark mb-0 me-1">
                                    <i class="fas fa-filter text-primary me-1"></i>Filter Status Telaah:
                                </label>
                                <select id="filter-status-kegiatan" class="form-select form-select-sm text-xs font-weight-bold" style="width: 300px;">
                                    <option value="">-- Semua Kegiatan ({{ count($rekapKegiatan) }}) --</option>
                                    <option value="BELUM DITELAAH">⚠️ Ada Metadata Belum Ditelaah / Menunggu Feedback</option>
                                    <option value="Perlu Perbaikan">🔧 Sudah Ditelaah — Perlu Perbaikan</option>
                                    <option value="Disetujui">✅ Sudah Ditelaah — Disetujui</option>
                                    <option value="Belum Upload Link">⏳ Belum Upload Link</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0 table-bordered" id="table-rekap-kegiatan">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 40px;">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 22%;">Dinas (OPD) &amp; Pembina</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 22%;">Kegiatan Statistik</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 17%;">Metadata Kegiatan<br>(MS-Keg)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 17%;">Metadata Variabel<br>(MS-Var)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 17%;">Metadata Indikator<br>(MS-Ind)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapKegiatan as $kIdx => $kRow)
                                        <tr class="{{ $kRow['has_pending_review'] ? 'table-warning' : '' }}">
                                            <td class="text-center text-xs font-weight-bold">{{ $kIdx + 1 }}</td>
                                            <td>
                                                <h6 class="mb-1 text-xs font-weight-bolder text-dark text-wrap">{{ $kRow['opd']->name }}</h6>
                                                <span class="badge badge-xs bg-light text-dark border">
                                                    <i class="fas fa-user-tie text-primary me-1"></i>Pembina: {{ $kRow['pembina']->name ?? 'Belum Ada' }}
                                                </span>
                                            </td>
                                            <td>
                                                <h6 class="mb-1 text-xs font-weight-bold text-dark text-wrap">{{ $kRow['kegiatan']->nama_kegiatan }}</h6>
                                                <small class="text-xxs text-secondary">
                                                    <i class="fas fa-database me-1"></i>{{ $kRow['jumlah_data'] }} Daftar Data Terkait
                                                </small>
                                            </td>

                                            @foreach(['kegiatan', 'variable', 'indikator'] as $tipeKey)
                                                @php $info = $kRow['tipe'][$tipeKey]; @endphp
                                                <td class="align-top text-center text-wrap p-2">
                                                    @if(!$info['has_link'])
                                                        <span class="badge badge-xs bg-light text-secondary border">Belum Upload Link</span>
                                                    @else
                                                        {{-- Indikator Ada Telaah atau Belum --}}
                                                        <div class="mb-1">
                                                            @if(!$info['ada_telaah'])
                                                                <span class="badge badge-xs bg-gradient-danger">
                                                                    <i class="fas fa-exclamation-circle me-1"></i>BELUM DITELAAH
                                                                </span>
                                                            @else
                                                                <span class="badge badge-xs bg-info-subtle text-info border border-info-subtle">
                                                                    <i class="fas fa-check me-1"></i>SUDAH DITELAAH
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Status Terakhir --}}
                                                        <div class="mb-1">
                                                            @if($info['status_terakhir'] === 'disetujui')
                                                                <span class="badge badge-xs bg-gradient-success">Status: Disetujui</span>
                                                            @elseif($info['status_terakhir'] === 'butuh_perbaikan')
                                                                <span class="badge badge-xs bg-gradient-warning text-dark">Status: Perlu Perbaikan</span>
                                                            @else
                                                                <span class="badge badge-xs bg-secondary">Status: Menunggu Review</span>
                                                            @endif
                                                        </div>

                                                        {{-- Info Reviewer & Catatan Terakhir --}}
                                                        @if($info['ada_telaah'])
                                                            <div class="text-xxs text-secondary mt-1">
                                                                Oleh: <strong>{{ $info['reviewer'] }}</strong>
                                                                @if($info['reviewed_at'])
                                                                    <br><span>({{ $info['reviewed_at']->format('d/m/Y H:i') }})</span>
                                                                @endif
                                                            </div>
                                                            @if($info['catatan'])
                                                                <div class="p-1 mt-1 rounded bg-white border text-start text-xxs text-dark" title="{{ $info['catatan'] }}">
                                                                    <strong>Feedback:</strong> {{ \Illuminate\Support\Str::limit($info['catatan'], 60) }}
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="text-xxs text-danger font-weight-bold mt-1">
                                                                Diupload {{ optional($info['submitted_at'])->format('d/m/Y H:i') ?? '-' }}
                                                            </div>
                                                        @endif

                                                        @if($info['link_url'])
                                                            <div class="mt-1">
                                                                <a href="{{ $info['link_url'] }}" target="_blank" class="text-xxs text-primary font-weight-bold text-decoration-underline">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Buka Link (Rev #{{ $info['rev_count'] }})
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="text-center align-middle">
                                                <a href="{{ route('pelaporan.metadata.index', ['opd_id' => $kRow['opd']->id]) }}" 
                                                   class="btn btn-xs {{ $kRow['has_pending_review'] ? 'bg-gradient-danger' : 'btn-outline-dark' }} mb-0">
                                                    <i class="fas fa-clipboard-check me-1"></i> {{ $kRow['has_pending_review'] ? 'Beri Telaah' : 'Buka' }}
                                                </a>
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

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        var tableOpd = $('#table-rekap-opd').DataTable({
            pageLength: 25,
            order: [],
            language: {
                search: "Cari Dinas / Pembina:",
                lengthMenu: "Tampilkan _MENU_ dinas",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ dinas",
                zeroRecords: "Tidak ada dinas yang sesuai filter",
                paginate: { previous: "<", next: ">" }
            }
        });

        var tableKegiatan = $('#table-rekap-kegiatan').DataTable({
            pageLength: 25,
            order: [],
            language: {
                search: "Cari Kegiatan / Dinas / Pembina:",
                lengthMenu: "Tampilkan _MENU_ kegiatan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ kegiatan",
                zeroRecords: "Tidak ada kegiatan yang sesuai filter",
                paginate: { previous: "<", next: ">" }
            }
        });

        $('#filter-status-opd').on('change', function() {
            var val = $(this).val();
            tableOpd.column(7).search(val, false, false).draw();
        });

        $('#filter-status-kegiatan').on('change', function() {
            var val = $(this).val();
            tableKegiatan.search(val).draw();
        });
    });
</script>
@endpush

