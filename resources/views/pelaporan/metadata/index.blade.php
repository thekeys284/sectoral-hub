@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pelaporan Metadata'])

    <div class="container-fluid py-4">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-white text-sm shadow-sm" role="alert">
                <span class="alert-icon"><i class="fas fa-check-circle me-2"></i></span>
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show text-white text-sm shadow-sm" role="alert">
                <span class="alert-icon"><i class="fas fa-exclamation-circle me-2"></i></span>
                <span class="alert-text">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card Tabel Pelaporan -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="font-weight-bolder text-dark mb-0">Pelaporan &amp; Telaah Metadata Statistik Sektoral</h6>
                    <p class="text-xs text-secondary mb-0">Kelola tautan dan instrumen telaah untuk Metadata Kegiatan (MS-Keg), Variabel (MS-Var), dan Indikator (MS-Ind).</p>
                    @if(isset($isPembina) && $isPembina && $selectedOpdId)
                        @if($selectedOpdId === 'all_binaan')
                            <span class="badge badge-sm bg-gradient-success mt-1"><i class="fas fa-check-circle me-1"></i> Menampilkan Seluruh OPD Binaan ({{ $opdBinaan->count() }} Dinas)</span>
                        @elseif($selectedOpdId === 'all')
                            <span class="badge badge-sm bg-gradient-info mt-1"><i class="fas fa-globe me-1"></i> Menampilkan Seluruh Dinas / OPD</span>
                        @elseif($opdBinaan->contains('id', (int)$selectedOpdId))
                            <span class="badge badge-sm bg-gradient-success mt-1"><i class="fas fa-check-circle me-1"></i> Menampilkan OPD Binaan Anda</span>
                        @else
                            <span class="badge badge-sm bg-gradient-secondary mt-1"><i class="fas fa-filter me-1"></i> Menampilkan OPD Lainnya</span>
                        @endif
                    @endif
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- INPUT PENCARIAN INSTAN (TANPA BATAS 10 BARIS) --}}
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-secondary"></i></span>
                        <input type="text" id="searchPelaporanInput" class="form-control form-control-sm border-start-0 text-xs" placeholder="Cari data, kegiatan, atau dinas...">
                    </div>

                    {{-- FILTER DROPDOWN KHUSUS ADMIN, WALIDATA & PEMBINA --}}
                    @if($isAdminOrPembina)
                        <form action="{{ route('pelaporan.metadata.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-secondary"></i></span>
                                <select name="opd_id" class="form-select form-select-sm border-start-0 text-xs font-weight-bold" style="min-width: 280px;" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedOpdId === 'all' ? 'selected' : '' }}>
                                        -- Semua Dinas / Perangkat Daerah --
                                    </option>

                                    @if(isset($opdBinaan) && $opdBinaan->isNotEmpty())
                                        <optgroup label="⭐ OPD Binaan Anda">
                                            @if($opdBinaan->count() > 1)
                                                <option value="all_binaan" {{ $selectedOpdId === 'all_binaan' ? 'selected' : '' }}>
                                                    -- Semua OPD Binaan Anda ({{ $opdBinaan->count() }} Dinas) --
                                                </option>
                                            @endif
                                            @foreach($opdBinaan as $binaan)
                                                <option value="{{ $binaan->id }}" {{ (string)$selectedOpdId === (string)$binaan->id ? 'selected' : '' }}>
                                                    {{ $binaan->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="🏢 Seluruh Dinas / OPD Lainnya">
                                            @foreach($opdList as $opd)
                                                <option value="{{ $opd->id }}" {{ (string)$selectedOpdId === (string)$opd->id ? 'selected' : '' }}>
                                                    {{ $opd->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @else
                                        @foreach($opdList as $opd)
                                            <option value="{{ $opd->id }}" {{ (string)$selectedOpdId === (string)$opd->id ? 'selected' : '' }}>
                                                {{ $opd->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @if($selectedOpdId && $selectedOpdId !== 'all')
                                <a href="{{ route('pelaporan.metadata.index') }}" class="btn btn-outline-secondary btn-sm mb-0 px-2 py-1" title="Reset Filter">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2 mt-3">
                <div class="table-responsive p-3">
                    <table class="table align-items-center mb-0 table-bordered" id="pelaporanMetadataTable">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 25%;">Daftar Data</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 25%;">Kegiatan Statistik &amp; Instansi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 16%;">Metadata Kegiatan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 16%;">Metadata Variabel</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 16%;">Metadata Indikator</th>
                            </tr>
                        </thead>
                        @forelse ($daftardata as $kegiatanId => $dataGroup)
                            @php 
                                $keg = $dataGroup->first()->kegiatan;
                                $opdName = $dataGroup->first()->opd->name ?? '-';
                                $allDataNames = $dataGroup->pluck('nama_data')->implode(' ');
                                $searchKey = strtolower(($keg?->nama_kegiatan ?? '') . ' ' . $opdName . ' ' . $allDataNames);
                            @endphp
                            <tbody class="pelaporan-group-tbody" data-search="{{ $searchKey }}">
                                @foreach ($dataGroup as $index => $item)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-sm text-wrap font-weight-normal text-dark">{{ $item->nama_data }}</h6>
                                    </td>

                                    @if($index === 0)
                                    <td rowspan="{{ $dataGroup->count() }}" class="align-top bg-gray-50 border-end">
                                        <h6 class="text-xs mb-1 text-wrap font-weight-bolder text-dark">{{ $keg?->nama_kegiatan ?? 'Tanpa Kegiatan' }}</h6>
                                        <span class="badge badge-sm bg-gradient-info text-wrap text-start">{{ $opdName }}</span>
                                    </td>

                                    {{-- Kolom 3 Tipe Metadata --}}
                                    @foreach(['kegiatan', 'variable', 'indikator'] as $tipe)
                                        @php
                                            $allSubmissions = $keg ? $keg->metadataSubmissions->where('tipe', $tipe)->sortBy('created_at') : collect();
                                            $latest = $allSubmissions->last();
                                            $submissionCount = $allSubmissions->count();
                                        @endphp
                                        <td rowspan="{{ $dataGroup->count() }}" class="align-top text-center text-wrap" style="min-width: 185px;">
                                            @if(!$latest)
                                                {{-- Belum ada link --}}
                                                @if($keg)
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-xs w-100 mb-0 btn-open-submit"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#sharedModalSubmit"
                                                        data-action="{{ route('pelaporan.metadata.submit', $keg->id) }}"
                                                        data-tipe="{{ $tipe }}"
                                                        data-tipe-label="{{ ucfirst($tipe) }}"
                                                        data-kegiatan="{{ $keg->nama_kegiatan }}"
                                                        data-is-revisi="0"
                                                        data-catatan="">
                                                    <i class="fas fa-plus me-1"></i> Isi Link
                                                </button>
                                                @endif
                                            @else
                                                {{-- Status Badge --}}
                                                <div class="mb-2">
                                                    @if($latest->status === 'disetujui')
                                                        <span class="badge badge-xs bg-gradient-success"><i class="fas fa-check-circle me-1"></i> Disetujui</span>
                                                    @elseif($latest->status === 'butuh_perbaikan')
                                                        <span class="badge badge-xs bg-gradient-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Perlu Perbaikan</span>
                                                    @else
                                                        <span class="badge badge-xs bg-gradient-secondary"><i class="fas fa-clock me-1"></i> Menunggu Review</span>
                                                    @endif

                                                    @if($submissionCount > 1)
                                                        <span class="badge badge-xs bg-light text-secondary border ms-1" title="Iterasi pengajuan">Rev #{{ $submissionCount }}</span>
                                                    @endif
                                                </div>

                                                {{-- Tombol Buka Link, Riwayat, Edit & Hapus (Khusus Admin) --}}
                                                <div class="d-flex justify-content-center flex-wrap gap-1 mb-2">
                                                    <a href="{{ $latest->link_url }}" target="_blank" class="btn btn-xs bg-gradient-info mb-0 px-2" title="Buka URL Metadata">
                                                        <i class="fas fa-external-link-alt me-1"></i> Buka
                                                    </a>
                                                    
                                                    <button type="button" class="btn btn-xs btn-outline-secondary mb-0 px-2" data-bs-toggle="modal" data-bs-target="#modalHistory_{{ $tipe }}_{{ $keg->id }}" title="Lihat linimasa riwayat revisi">
                                                        <i class="fas fa-history"></i> ({{ $submissionCount }})
                                                    </button>

                                                    @if(isset($isAdmin) && $isAdmin)
                                                        <button type="button"
                                                                class="btn btn-xs btn-outline-warning text-dark mb-0 px-2 btn-open-edit-link"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#sharedModalEditLink"
                                                                data-action="{{ route('pelaporan.metadata.submission.update', $latest->id) }}"
                                                                data-tipe-label="{{ ucfirst($tipe) }}"
                                                                data-kegiatan="{{ $keg->nama_kegiatan }}"
                                                                data-url="{{ $latest->link_url }}"
                                                                title="Edit Link Metadata">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <form action="{{ route('pelaporan.metadata.submission.destroy', $latest->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tautan metadata ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-xs btn-outline-danger mb-0 px-2" title="Hapus Link Metadata">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>

                                                {{-- Kotak Catatan Pembina Terbaru --}}
                                                @if($latest->catatan_pembina)
                                                    <div class="alert alert-secondary py-2 px-2 mb-2 text-start border border-warning shadow-none" style="background-color: #fff9e6;">
                                                        <div class="d-flex align-items-start gap-1">
                                                            <i class="fas fa-comment-dots text-warning mt-1"></i>
                                                            <div>
                                                                <strong class="text-xxs text-dark d-block">Catatan Pembina:</strong>
                                                                <p class="text-xxs text-dark mb-0">{{ $latest->catatan_pembina }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Tombol Kirim Revisi (Untuk Produsen) --}}
                                                @if(!$isAdminOrPembina || $latest->status === 'butuh_perbaikan')
                                                    <button type="button"
                                                            class="btn btn-xs bg-gradient-warning text-dark w-100 mb-1 btn-open-submit"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#sharedModalSubmit"
                                                            data-action="{{ route('pelaporan.metadata.submit', $keg->id) }}"
                                                            data-tipe="{{ $tipe }}"
                                                            data-tipe-label="{{ ucfirst($tipe) }}"
                                                            data-kegiatan="{{ $keg->nama_kegiatan }}"
                                                            data-is-revisi="1"
                                                            data-catatan="{{ $latest->catatan_pembina }}">
                                                        <i class="fas fa-redo me-1"></i> Kirim Link Revisi
                                                    </button>
                                                    <br>
                                                @endif

                                                {{-- Tombol Telaah / Review (Untuk Admin & Pembina) --}}
                                                @if($isAdminOrPembina)
                                                    <button type="button"
                                                            class="btn btn-outline-dark btn-xs w-100 mb-0 mt-1 btn-open-review"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#sharedModalReview"
                                                            data-action="{{ route('pelaporan.metadata.review', $latest->id) }}"
                                                            data-tipe-label="{{ ucfirst($tipe) }}"
                                                            data-kegiatan="{{ $keg->nama_kegiatan }}"
                                                            data-url="{{ $latest->link_url }}"
                                                            data-status="{{ $latest->status === 'disetujui' ? 'disetujui' : 'butuh_perbaikan' }}"
                                                            data-catatan="{{ $latest->catatan_pembina }}">
                                                        <i class="fas fa-clipboard-check me-1"></i> {{ $latest->status === 'pending' ? 'Beri Telaah' : 'Edit Telaah' }}
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        @empty
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-secondary text-sm">
                                        Tidak ditemukan kegiatan statistik untuk dinas yang dipilih.
                                    </td>
                                </tr>
                            </tbody>
                        @endforelse
                        <tbody id="noSearchMatchRow" class="d-none">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary text-sm">
                                    Tidak ditemukan data atau kegiatan yang cocok dengan kata kunci pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>

    {{-- ========================================================================= --}}
    {{-- SHARED MODALS (HANYA 3 MODAL UNTUK SELURUH BARIS AGAR RENDER SUPER CEPAT) --}}
    {{-- ========================================================================= --}}

    {{-- 1. SHARED MODAL INPUT / REVISI LINK --}}
    <div class="modal fade" id="sharedModalSubmit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="sharedFormSubmit" method="POST" class="w-100">
                @csrf
                <input type="hidden" name="tipe" id="submitTipeInput">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h6 class="modal-title font-weight-bold text-white mb-0" id="submitModalTitle">
                            <i class="fas fa-link me-2"></i>Input Link Metadata
                        </h6>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="bg-gray-100 p-3 rounded mb-3">
                            <span class="text-xxs text-secondary d-block font-weight-bold">KEGIATAN STATISTIK:</span>
                            <span class="text-xs font-weight-bold text-dark" id="submitKegiatanName"></span>
                        </div>

                        <div class="alert alert-warning py-2 px-3 text-xs mb-3 d-none" id="submitCatatanBox">
                            <strong><i class="fas fa-exclamation-circle me-1"></i> Catatan Pembina yang Harus Diperbaiki:</strong>
                            <div class="mt-1" id="submitCatatanText"></div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-xs font-weight-bold">Tautan / URL Metadata <span class="text-danger">*</span></label>
                            <input type="url" name="link_url" id="submitLinkUrl" class="form-control" placeholder="https://..." required>
                            <small class="text-xxs text-muted mt-1 d-block">Masukkan URL dokumen/sheet metadata terbaru.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-paper-plane me-1"></i> Simpan &amp; Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. SHARED MODAL TELAAH & CATATAN PERBAIKAN (ADMIN / PEMBINA) --}}
    <div class="modal fade" id="sharedModalReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="sharedFormReview" method="POST" class="w-100">
                @csrf
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-dark text-white">
                        <h6 class="modal-title font-weight-bold text-white mb-0" id="reviewModalTitle">
                            <i class="fas fa-clipboard-check me-2"></i>Telaah Metadata
                        </h6>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="bg-gray-100 p-3 rounded mb-3">
                            <div class="text-xxs text-secondary font-weight-bold mb-1">KEGIATAN STATISTIK:</div>
                            <div class="text-xs font-weight-bold text-dark mb-2" id="reviewKegiatanName"></div>
                            <div class="text-xxs text-secondary font-weight-bold mb-1">TAUTAN METADATA YANG DITELAAH:</div>
                            <a href="#" target="_blank" id="reviewLinkAnchor" class="text-xs text-primary font-weight-bold text-break text-decoration-underline"></a>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bold">Status Hasil Telaah <span class="text-danger">*</span></label>
                            <select name="status" id="reviewStatusSelect" class="form-select text-sm" required>
                                <option value="disetujui">✅ Disetujui</option>
                                <option value="butuh_perbaikan">⚠️ Perlu Perbaikan (Ada Catatan Revisi)</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-xs font-weight-bold">Catatan Pembina / Masukan Perbaikan</label>
                            <textarea name="catatan_pembina" id="reviewCatatanInput" class="form-control text-sm" rows="4" placeholder="Tuliskan masukan teknis perbaikan..."></textarea>
                            <small class="text-xxs text-muted mt-1 d-block">Catatan ini langsung tampil pada akun Produsen Data untuk diperbaiki.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn bg-gradient-dark btn-sm mb-0">
                            <i class="fas fa-save me-1"></i> Simpan Hasil Telaah
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. SHARED MODAL EDIT LINK (KHUSUS ADMIN) --}}
    @if(isset($isAdmin) && $isAdmin)
    <div class="modal fade" id="sharedModalEditLink" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="sharedFormEditLink" method="POST" class="w-100">
                @csrf
                @method('PUT')
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-gradient-warning text-dark">
                        <h6 class="modal-title font-weight-bold text-dark mb-0" id="editLinkModalTitle">
                            <i class="fas fa-edit me-2"></i>Edit Link Metadata
                        </h6>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="bg-gray-100 p-3 rounded mb-3">
                            <span class="text-xxs text-secondary d-block font-weight-bold">KEGIATAN STATISTIK:</span>
                            <span class="text-xs font-weight-bold text-dark" id="editLinkKegiatanName"></span>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-xs font-weight-bold">Tautan / URL Metadata <span class="text-danger">*</span></label>
                            <input type="url" name="link_url" id="editLinkUrlInput" class="form-control" placeholder="https://..." required>
                            <small class="text-xxs text-muted mt-1 d-block">Perbarui tautan metadata secara langsung.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn bg-gradient-warning text-dark btn-sm mb-0">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- 4. MODAL TIMELINE RIWAYAT REVISI (Hanya dirender untuk item yang sudah punya submission) --}}
    @foreach ($daftardata as $kegiatanId => $dataGroup)
        @php $keg = $dataGroup->first()->kegiatan; @endphp
        @if($keg)
            @foreach(['kegiatan', 'variable', 'indikator'] as $tipe)
                @php
                    $allSubmissions = $keg->metadataSubmissions->where('tipe', $tipe)->sortBy('created_at');
                @endphp
                @if($allSubmissions->isNotEmpty())
                <div class="modal fade" id="modalHistory_{{ $tipe }}_{{ $keg->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content shadow-lg border-0">
                            <div class="modal-header bg-gradient-secondary text-white">
                                <h6 class="modal-title font-weight-bold text-white mb-0">
                                    <i class="fas fa-history me-2"></i>Riwayat Telaah &amp; Revisi Metadata {{ ucfirst($tipe) }}
                                </h6>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                                <div class="timeline timeline-one-side">
                                    @foreach($allSubmissions as $revIndex => $submission)
                                        <div class="timeline-block mb-3">
                                            <span class="timeline-step {{ $submission->status === 'disetujui' ? 'badge-success' : ($submission->status === 'butuh_perbaikan' ? 'badge-warning' : 'badge-secondary') }}">
                                                <i class="fas {{ $submission->status === 'disetujui' ? 'fa-check' : ($submission->status === 'butuh_perbaikan' ? 'fa-exclamation' : 'fa-clock') }} text-white"></i>
                                            </span>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="text-dark text-sm font-weight-bold mb-0">
                                                        Pengajuan / Revisi ke-{{ $revIndex + 1 }}
                                                    </h6>
                                                    <small class="text-xs text-secondary">{{ optional($submission->created_at)->format('d M Y, H:i') ?? '-' }}</small>
                                                </div>

                                                <p class="text-xs text-secondary mt-1 mb-2">
                                                    <strong>Tautan yang Dikirim:</strong> 
                                                    <a href="{{ $submission->link_url }}" target="_blank" class="text-primary text-break text-decoration-underline">
                                                        {{ $submission->link_url }}
                                                    </a>
                                                </p>

                                                @if($submission->status === 'pending')
                                                    <span class="badge badge-xs bg-secondary">Menunggu Telaah Pembina</span>
                                                @else
                                                    <div class="p-2 rounded border {{ $submission->status === 'disetujui' ? 'border-success bg-gray-50' : 'border-warning bg-light' }}">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="badge badge-xs {{ $submission->status === 'disetujui' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                                {{ $submission->status === 'disetujui' ? 'Disetujui' : 'Perlu Perbaikan' }}
                                                            </span>
                                                            <small class="text-xxs text-secondary">
                                                                Ditelaah oleh: {{ $submission->reviewer->name ?? 'Pembina Data' }} ({{ optional($submission->reviewed_at)->format('d M Y, H:i') ?? '-' }})
                                                            </small>
                                                        </div>
                                                        <p class="text-xs text-dark mb-0">
                                                            <strong>Catatan:</strong> {{ $submission->catatan_pembina ?? 'Tidak ada catatan khusus.' }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary btn-sm mb-0" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        @endif
    @endforeach
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Live Search Filter (menyaring per grup Kegiatan tanpa merusak rowspan dan tanpa batas 10 baris)
        const searchInput = document.getElementById('searchPelaporanInput');
        const groups = document.querySelectorAll('tbody.pelaporan-group-tbody');
        const noMatchRow = document.getElementById('noSearchMatchRow');

        if (searchInput && groups.length > 0) {
            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim().toLowerCase();
                let visibleCount = 0;

                groups.forEach(function(tbody) {
                    const text = tbody.getAttribute('data-search') || '';
                    if (!keyword || text.includes(keyword)) {
                        tbody.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        tbody.classList.add('d-none');
                    }
                });

                if (noMatchRow) {
                    noMatchRow.classList.toggle('d-none', visibleCount > 0);
                }
            });
        }

        // 2. Handler Shared Modal Input / Revisi Link
        document.addEventListener('click', function(e) {
            const submitBtn = e.target.closest('.btn-open-submit');
            if (submitBtn) {
                document.getElementById('sharedFormSubmit').setAttribute('action', submitBtn.dataset.action);
                document.getElementById('submitTipeInput').value = submitBtn.dataset.tipe;
                document.getElementById('submitKegiatanName').textContent = submitBtn.dataset.kegiatan;
                document.getElementById('submitLinkUrl').value = '';

                const isRevisi = submitBtn.dataset.isRevisi === '1';
                document.getElementById('submitModalTitle').innerHTML =
                    '<i class="fas fa-link me-2"></i>' + (isRevisi ? 'Kirim Link Revisi' : 'Input Link') + ' Metadata ' + submitBtn.dataset.tipeLabel;

                const catatanBox = document.getElementById('submitCatatanBox');
                const catatanText = document.getElementById('submitCatatanText');
                if (submitBtn.dataset.catatan) {
                    catatanText.textContent = submitBtn.dataset.catatan;
                    catatanBox.classList.remove('d-none');
                } else {
                    catatanBox.classList.add('d-none');
                }
            }

            // 3. Handler Shared Modal Telaah / Review
            const reviewBtn = e.target.closest('.btn-open-review');
            if (reviewBtn) {
                document.getElementById('sharedFormReview').setAttribute('action', reviewBtn.dataset.action);
                document.getElementById('reviewModalTitle').innerHTML =
                    '<i class="fas fa-clipboard-check me-2"></i>Telaah Metadata ' + reviewBtn.dataset.tipeLabel;
                document.getElementById('reviewKegiatanName').textContent = reviewBtn.dataset.kegiatan;

                const linkAnchor = document.getElementById('reviewLinkAnchor');
                linkAnchor.setAttribute('href', reviewBtn.dataset.url);
                linkAnchor.innerHTML = '<i class="fas fa-external-link-alt me-1"></i>' + reviewBtn.dataset.url;

                document.getElementById('reviewStatusSelect').value = reviewBtn.dataset.status;
                document.getElementById('reviewCatatanInput').value = reviewBtn.dataset.catatan || '';
            }

            // 4. Handler Shared Modal Edit Link (Admin)
            const editBtn = e.target.closest('.btn-open-edit-link');
            if (editBtn) {
                document.getElementById('sharedFormEditLink').setAttribute('action', editBtn.dataset.action);
                document.getElementById('editLinkModalTitle').innerHTML =
                    '<i class="fas fa-edit me-2"></i>Edit Link Metadata ' + editBtn.dataset.tipeLabel;
                document.getElementById('editLinkKegiatanName').textContent = editBtn.dataset.kegiatan;
                document.getElementById('editLinkUrlInput').value = editBtn.dataset.url || '';
            }
        });
    });
</script>
@endpush


@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Live Search Filter (menyaring per grup Kegiatan tanpa merusak rowspan dan tanpa batas 10 baris)
        const searchInput = document.getElementById('searchPelaporanInput');
        const groups = document.querySelectorAll('tbody.pelaporan-group-tbody');
        const noMatchRow = document.getElementById('noSearchMatchRow');

        if (searchInput && groups.length > 0) {
            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim().toLowerCase();
                let visibleCount = 0;

                groups.forEach(function(tbody) {
                    const text = tbody.getAttribute('data-search') || '';
                    if (!keyword || text.includes(keyword)) {
                        tbody.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        tbody.classList.add('d-none');
                    }
                });

                if (noMatchRow) {
                    noMatchRow.classList.toggle('d-none', visibleCount > 0);
                }
            });
        }

        // 2. Handler Shared Modal Input / Revisi Link
        document.addEventListener('click', function(e) {
            const submitBtn = e.target.closest('.btn-open-submit');
            if (submitBtn) {
                document.getElementById('sharedFormSubmit').setAttribute('action', submitBtn.dataset.action);
                document.getElementById('submitTipeInput').value = submitBtn.dataset.tipe;
                document.getElementById('submitKegiatanName').textContent = submitBtn.dataset.kegiatan;
                document.getElementById('submitLinkUrl').value = '';

                const isRevisi = submitBtn.dataset.isRevisi === '1';
                document.getElementById('submitModalTitle').innerHTML =
                    '<i class="fas fa-link me-2"></i>' + (isRevisi ? 'Kirim Link Revisi' : 'Input Link') + ' Metadata ' + submitBtn.dataset.tipeLabel;

                const catatanBox = document.getElementById('submitCatatanBox');
                const catatanText = document.getElementById('submitCatatanText');
                if (submitBtn.dataset.catatan) {
                    catatanText.textContent = submitBtn.dataset.catatan;
                    catatanBox.classList.remove('d-none');
                } else {
                    catatanBox.classList.add('d-none');
                }
            }

            // 3. Handler Shared Modal Telaah / Review
            const reviewBtn = e.target.closest('.btn-open-review');
            if (reviewBtn) {
                document.getElementById('sharedFormReview').setAttribute('action', reviewBtn.dataset.action);
                document.getElementById('reviewModalTitle').innerHTML =
                    '<i class="fas fa-clipboard-check me-2"></i>Telaah Metadata ' + reviewBtn.dataset.tipeLabel;
                document.getElementById('reviewKegiatanName').textContent = reviewBtn.dataset.kegiatan;

                const linkAnchor = document.getElementById('reviewLinkAnchor');
                linkAnchor.setAttribute('href', reviewBtn.dataset.url);
                linkAnchor.innerHTML = '<i class="fas fa-external-link-alt me-1"></i>' + reviewBtn.dataset.url;

                document.getElementById('reviewStatusSelect').value = reviewBtn.dataset.status;
                document.getElementById('reviewCatatanInput').value = reviewBtn.dataset.catatan || '';
            }

            // 4. Handler Shared Modal Edit Link (Admin)
            const editBtn = e.target.closest('.btn-open-edit-link');
            if (editBtn) {
                document.getElementById('sharedFormEditLink').setAttribute('action', editBtn.dataset.action);
                document.getElementById('editLinkModalTitle').innerHTML =
                    '<i class="fas fa-edit me-2"></i>Edit Link Metadata ' + editBtn.dataset.tipeLabel;
                document.getElementById('editLinkKegiatanName').textContent = editBtn.dataset.kegiatan;
                document.getElementById('editLinkUrlInput').value = editBtn.dataset.url || '';
            }
        });
    });
</script>
@endpush