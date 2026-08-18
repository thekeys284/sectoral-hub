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
                    <h6 class="font-weight-bolder text-dark mb-0">Pelaporan & Telaah Metadata Statistik Sektoral</h6>
                    <p class="text-xs text-secondary mb-0">Kelola tautan dan instrumen telaah untuk Metadata Kegiatan (MS-Keg), Variabel (MS-Var), dan Indikator (MS-Ind).</p>
                </div>

                {{-- FILTER DROPDOWN KHUSUS ADMIN & PEMBINA --}}
                @if($isAdminOrPembina)
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('pelaporan.metadata.index') }}" method="GET" class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-secondary"></i></span>
                                <select name="opd_id" class="form-select form-select-sm border-start-0 text-xs font-weight-bold" style="min-width: 280px;" onchange="this.form.submit()">
                                    <option value="">-- Pilih Dinas / Perangkat Daerah --</option>
                                    @foreach($opdList as $opd)
                                        <option value="{{ $opd->id }}" {{ $selectedOpdId == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($selectedOpdId)
                                <a href="{{ route('pelaporan.metadata.index') }}" class="btn btn-outline-secondary btn-sm mb-0 px-2 py-1" title="Reset Tampilan">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                @endif
            </div>

            <div class="card-body px-0 pt-0 pb-2 mt-3">
                <div class="table-responsive p-3">
                    <table class="table align-items-center mb-0 table-bordered">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 25%;">Daftar Data</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 25%;">Kegiatan Statistik & Instansi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 16%;">Metadata Kegiatan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 16%;">Metadata Variabel</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" style="width: 16%;">Metadata Indikator</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Kondisi 1: Admin/Pembina belum memilih dinas apapun --}}
                            @if($isAdminOrPembina && empty($selectedOpdId))
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-center">
                                            <i class="fas fa-hand-pointer text-secondary fa-2x mb-3 opacity-5"></i>
                                            <h6 class="text-sm font-weight-bold text-dark mb-1">Silakan Pilih Dinas / Perangkat Daerah Terlebih Dahulu</h6>
                                            <p class="text-xs text-secondary mb-0">Pilih salah satu instansi dari dropdown di pojok kanan atas untuk memuat daftar data dan kegiatan terkait.</p>
                                        </div>
                                    </td>
                                </tr>
                            {{-- Kondisi 2: Data sudah dipilih / Role Produsen --}}
                            @else
                                @forelse ($daftardata as $kegiatanId => $dataGroup)
                                    @php 
                                        $keg = $dataGroup->first()->kegiatan; 
                                    @endphp
                                    @foreach ($dataGroup as $index => $item)
                                    <tr>
                                        <td>
                                            <h6 class="mb-0 text-sm text-wrap font-weight-normal text-dark">{{ $item->nama_data }}</h6>
                                        </td>

                                        @if($index === 0)
                                        <td rowspan="{{ $dataGroup->count() }}" class="align-top bg-gray-50 border-end">
                                            <h6 class="text-xs mb-1 text-wrap font-weight-bolder text-dark">{{ $keg?->nama_kegiatan ?? 'Tanpa Kegiatan' }}</h6>
                                            <span class="badge badge-sm bg-gradient-info text-wrap text-start">{{ $item->opd->name ?? '-' }}</span>
                                        </td>

                                        {{-- Kolom 3 Tipe Metadata --}}
                                        @foreach(['kegiatan', 'variable', 'indikator'] as $tipe)
                                            @php
                                                $allSubmissions = $keg ? $keg->metadataSubmissions->where('tipe', $tipe)->sortBy('created_at') : collect();
                                                $latest = $allSubmissions->last();
                                                $submissionCount = $allSubmissions->count();
                                            @endphp
                                            <td rowspan="{{ $dataGroup->count() }}" class="align-top text-center" style="min-width: 185px;">
                                                @if(!$latest)
                                                    {{-- Belum ada link --}}
                                                    <button type="button" class="btn btn-outline-danger btn-xs w-100 mb-0" data-bs-toggle="modal" data-bs-target="#modalSubmit_{{ $tipe }}_{{ $keg?->id }}">
                                                        <i class="fas fa-plus me-1"></i> Isi Link
                                                    </button>
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

                                                    {{-- Tombol Buka Link & Tombol Riwayat --}}
                                                    <div class="d-flex justify-content-center gap-1 mb-2">
                                                        <a href="{{ $latest->link_url }}" target="_blank" class="btn btn-xs bg-gradient-info mb-0" title="Buka URL Metadata">
                                                            <i class="fas fa-external-link-alt me-1"></i> Buka
                                                        </a>
                                                        
                                                        <button type="button" class="btn btn-xs btn-outline-secondary mb-0" data-bs-toggle="modal" data-bs-target="#modalHistory_{{ $tipe }}_{{ $keg->id }}" title="Lihat linimasa riwayat revisi">
                                                            <i class="fas fa-history"></i> ({{ $submissionCount }})
                                                        </button>
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
                                                        <button type="button" class="btn btn-xs bg-gradient-warning text-dark w-100 mb-1" data-bs-toggle="modal" data-bs-target="#modalSubmit_{{ $tipe }}_{{ $keg->id }}">
                                                            <i class="fas fa-redo me-1"></i> Kirim Link Revisi
                                                        </button>
                                                        <br>
                                                    @endif

                                                    {{-- Tombol Telaah / Review (Untuk Admin & Pembina) --}}
                                                    @if($isAdminOrPembina)
                                                        <button type="button" class="btn btn-outline-dark btn-xs w-100 mb-0 mt-1" data-bs-toggle="modal" data-bs-target="#modalReview_{{ $latest->id }}">
                                                            <i class="fas fa-clipboard-check me-1"></i> {{ $latest->status === 'pending' ? 'Beri Telaah' : 'Edit Telaah' }}
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                        @endif
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary text-sm">
                                            Tidak ditemukan kegiatan statistik untuk dinas yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>

    {{-- ========================================================================= --}}
    {{-- MODALS SECTION                                                           --}}
    {{-- ========================================================================= --}}
    @foreach ($daftardata as $kegiatanId => $dataGroup)
        @php $keg = $dataGroup->first()->kegiatan; @endphp
        @if($keg)
            @foreach(['kegiatan', 'variable', 'indikator'] as $tipe)
                @php
                    $allSubmissions = $keg->metadataSubmissions->where('tipe', $tipe)->sortBy('created_at');
                    $latest = $allSubmissions->last();
                @endphp

                {{-- 1. MODAL INPUT / REVISI LINK (PRODUSEN) --}}
                <div class="modal fade" id="modalSubmit_{{ $tipe }}_{{ $keg->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('pelaporan.metadata.submit', $keg->id) }}" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="tipe" value="{{ $tipe }}">
                            <div class="modal-content shadow-lg border-0">
                                <div class="modal-header bg-gradient-primary text-white">
                                    <h6 class="modal-title font-weight-bold text-white mb-0">
                                        <i class="fas fa-link me-2"></i>{{ $latest ? 'Kirim Link Revisi' : 'Input Link' }} Metadata {{ ucfirst($tipe) }}
                                    </h6>
                                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="bg-gray-100 p-3 rounded mb-3">
                                        <span class="text-xxs text-secondary d-block font-weight-bold">KEGIATAN STATISTIK:</span>
                                        <span class="text-xs font-weight-bold text-dark">{{ $keg->nama_kegiatan }}</span>
                                    </div>

                                    @if($latest && $latest->catatan_pembina)
                                        <div class="alert alert-warning py-2 px-3 text-xs mb-3">
                                            <strong><i class="fas fa-exclamation-circle me-1"></i> Catatan Pembina yang Harus Diperbaiki:</strong>
                                            <div class="mt-1">{{ $latest->catatan_pembina }}</div>
                                        </div>
                                    @endif

                                    <div class="mb-0">
                                        <label class="form-label text-xs font-weight-bold">Tautan / URL Metadata Baru (Revisi) <span class="text-danger">*</span></label>
                                        <input type="url" name="link_url" class="form-control" placeholder="https://..." required>
                                        <small class="text-xxs text-muted mt-1 d-block">Masukkan URL dokumen/sheet metadata terbaru yang telah diperbaiki.</small>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn bg-gradient-primary btn-sm mb-0">
                                        <i class="fas fa-paper-plane me-1"></i> Simpan & Kirim
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 2. MODAL TELAAH & CATATAN PERBAIKAN (ADMIN / PEMBINA) --}}
                @if($latest)
                <div class="modal fade" id="modalReview_{{ $latest->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('pelaporan.metadata.review', $latest->id) }}" method="POST" class="w-100">
                            @csrf
                            <div class="modal-content shadow-lg border-0">
                                <div class="modal-header bg-gradient-dark text-white">
                                    <h6 class="modal-title font-weight-bold text-white mb-0">
                                        <i class="fas fa-clipboard-check me-2"></i>Telaah Metadata {{ ucfirst($tipe) }}
                                    </h6>
                                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body p-4">
                                    <div class="bg-gray-100 p-3 rounded mb-3">
                                        <div class="text-xxs text-secondary font-weight-bold mb-1">KEGIATAN STATISTIK:</div>
                                        <div class="text-xs font-weight-bold text-dark mb-2">{{ $keg->nama_kegiatan }}</div>
                                        
                                        <div class="text-xxs text-secondary font-weight-bold mb-1">TAUTAN METADATA YANG DITELAAH:</div>
                                        <a href="{{ $latest->link_url }}" target="_blank" class="text-xs text-primary font-weight-bold text-break text-decoration-underline">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $latest->link_url }}
                                        </a>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-xs font-weight-bold">Status Hasil Telaah <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select text-sm" required>
                                            <option value="disetujui" {{ $latest->status === 'disetujui' ? 'selected' : '' }}>
                                                ✅ Disetujui
                                            </option>
                                            <option value="butuh_perbaikan" {{ $latest->status === 'butuh_perbaikan' ? 'selected' : '' }}>
                                                ⚠️ Perlu Perbaikan (Ada Catatan Revisi)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label text-xs font-weight-bold">Catatan Pembina / Masukan Perbaikan</label>
                                        <textarea name="catatan_pembina" 
                                                  class="form-control text-sm" 
                                                  rows="4" 
                                                  placeholder="Tuliskan masukan teknis perbaikan...">{{ old('catatan_pembina', $latest->catatan_pembina) }}</textarea>
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
                @endif

                {{-- 3. MODAL TIMELINE RIWAYAT REVISI --}}
                @if($allSubmissions->isNotEmpty())
                <div class="modal fade" id="modalHistory_{{ $tipe }}_{{ $keg->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content shadow-lg border-0">
                            <div class="modal-header bg-gradient-secondary text-white">
                                <h6 class="modal-title font-weight-bold text-white mb-0">
                                    <i class="fas fa-history me-2"></i>Riwayat Telaah & Revisi Metadata {{ ucfirst($tipe) }}
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
                                                    <small class="text-xs text-secondary">{{ $submission->created_at->format('d M Y, H:i') }}</small>
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
                                                                Ditelusuri oleh: {{ $submission->reviewer->name ?? 'Pembina Data' }} ({{ optional($submission->reviewed_at)->format('d M Y, H:i') ?? '-' }})
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