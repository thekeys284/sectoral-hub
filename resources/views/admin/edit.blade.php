@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Edit Informasi Event'])
<div class="container-fluid py-2">
    <div class="row g-0">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <a href="{{ route('admin.events.index') }}" class="text-decoration-none text-secondary text-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Event
                </a>
                <h3 class="font-weight-bold mt-1 mb-0">Edit Informasi Event</h3>
                <p class="text-secondary text-sm mb-0"></p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Informasi Utama</h5>
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Judul Event / Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $event->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="pelatihan" {{ old('category', $event->category) == 'pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                                    <option value="pembinaan" {{ old('category', $event->category) == 'pembinaan' ? 'selected' : '' }}>Pembinaan</option>
                                    <option value="sosialisasi" {{ old('category', $event->category) == 'sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                                    <option value="rapat" {{ old('category', $event->category) == 'rapat' ? 'selected' : '' }}>Rapat</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Lokasi / Keterangan Tempat</label>
                                <input type="text" name="lokasi_event" class="form-control" value="{{ old('lokasi_event', $event->lokasi_event) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Deskripsi Pelatihan</label>
                            <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Tautan Streaming, Materi & Sertifikat</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Link Zoom / Virtual Meeting</label>
                                <input type="url" name="meeting_link" class="form-control @error('meeting_link') is-invalid @enderror" value="{{ old('meeting_link', $event->meeting_link) }}" placeholder="https://zoom.us/j/...">
                                @error('meeting_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Link YouTube</label>
                                <input type="url" name="youtube_link" class="form-control @error('youtube_link') is-invalid @enderror" value="{{ old('youtube_link', $event->youtube_link) }}" placeholder="https://youtube.com/...">
                                @error('youtube_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Link Materi Pelatihan</label>
                                <input type="url" name="link_materi" class="form-control @error('link_materi') is-invalid @enderror" value="{{ old('link_materi', $event->link_materi) }}" placeholder="https://youtube.com/...">
                                @error('link_materi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Link Dokumentasi</label>
                                <input type="url" name="doc_link" class="form-control @error('doc_link') is-invalid @enderror" value="{{ old('doc_link', $event->doc_link) }}" placeholder="https://drive.google.com/...">
                                @error('doc_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mt-3 pt-3 border-top">
                                <label class="form-label font-weight-bold d-block">Metode Sertifikat Pelatihan</label>
                                
                                <div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="radio" name="certificate_type" id="cert_type_all" value="all" 
                                           {{ old('certificate_type', $event->certificate_type ?? 'all') === 'all' ? 'checked' : '' }}
                                           onchange="toggleCertificateInput()">
                                    <label class="form-check-label font-weight-bold" for="cert_type_all">
                                        <i class="fas fa-link text-primary me-1"></i> Satu Link untuk Semua Peserta
                                    </label>
                                </div>
                                
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="certificate_type" id="cert_type_unique" value="unique" 
                                           {{ old('certificate_type', $event->certificate_type ?? 'all') === 'unique' ? 'checked' : '' }}
                                           onchange="toggleCertificateInput()">
                                    <label class="form-check-label font-weight-bold" for="cert_type_unique">
                                        <i class="fas fa-id-card text-success me-1"></i> Unik per Peserta (Generate / Personal)
                                    </label>
                                </div>

                                <!-- Input URL jika Satu Link -->
                                <div id="cert_link_wrapper" class="mt-2" style="{{ old('certificate_type', $event->certificate_type ?? 'all') === 'unique' ? 'display: none;' : '' }}">
                                    <label class="form-label small text-muted font-weight-bold">Link Sertifikat (Google Drive / Cloud Folder):</label>
                                    <input type="url" name="certificate_link" class="form-control @error('certificate_link') is-invalid @enderror" 
                                           value="{{ old('certificate_link', $event->certificate_link) }}" 
                                           placeholder="https://drive.google.com/drive/folders/...">
                                    <small class="text-muted d-block mt-1">Setiap peserta yang lulus akan mengakses link yang sama ini.</small>
                                    @error('certificate_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Keterangan jika Unik per Peserta -->
                                <div id="cert_unique_info" class="mt-2 alert alert-light border small py-2 mb-0" style="{{ old('certificate_type', $event->certificate_type ?? 'all') === 'all' ? 'display: none;' : '' }}">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Mode Unik Aktif:</strong> Setiap peserta yang lulus akan memiliki tautan/file sertifikat individu masing-masing (disediakan via generator sertifikat atau diatur per peserta).
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Banner & Virtual Background</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ganti Image Banner</label>
                                <input type="file" name="image_banner" class="form-control mb-2" accept="image/*">
                                @if($event->image_banner)
                                    <small class="text-muted d-block">Banner saat ini: <a href="{{ asset('storage/' . $event->image_banner) }}" target="_blank">Lihat Gambar</a></small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ganti Virtual Background</label>
                                <input type="file" name="virtual_bg" class="form-control mb-2" accept="image/*">
                                @if($event->virtual_bg)
                                    <small class="text-muted d-block">BG saat ini: <a href="{{ asset('storage/' . $event->virtual_bg) }}" target="_blank">Lihat Gambar</a></small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Jadwal & Absensi</h5>

                        <div class="mb-3">
                            <label class="form-label">Waktu Mulai Event</label>
                            <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at', $event->start_at ? $event->start_at->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu Selesai Event</label>
                            <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', $event->end_at ? $event->end_at->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label class="form-label font-weight-bold d-block">Metode Presensi / Absensi</label>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="absensi_type" id="absensi_type_web" value="web"
                                       {{ old('absensi_type', $event->absensi_type ?? 'web') === 'web' ? 'checked' : '' }}
                                       onchange="toggleAbsensiInput()">
                                <label class="form-check-label font-weight-bold" for="absensi_type_web">
                                    <i class="fas fa-check-circle text-success me-1"></i> Presensi di Web (Mandiri)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="absensi_type" id="absensi_type_link" value="link"
                                       {{ old('absensi_type', $event->absensi_type ?? 'web') === 'link' ? 'checked' : '' }}
                                       onchange="toggleAbsensiInput()">
                                <label class="form-check-label font-weight-bold" for="absensi_type_link">
                                    <i class="fas fa-external-link-alt text-primary me-1"></i> Presensi dari Link Luar
                                </label>
                            </div>

                            <div id="absensi_link_wrapper" class="mt-2" style="{{ old('absensi_type', $event->absensi_type ?? 'web') === 'link' ? '' : 'display: none;' }}">
                                <label class="form-label small text-muted font-weight-bold">Tautan / URL Presensi Luar:</label>
                                <input type="url" name="absensi_link" class="form-control @error('absensi_link') is-invalid @enderror"
                                       value="{{ old('absensi_link', $event->absensi_link) }}"
                                       placeholder="https://forms.gle/...">
                                <small class="text-muted d-block mt-1">Peserta akan diarahkan ke tautan ini saat mengisi daftar hadir.</small>
                                @error('absensi_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Buka Absensi</label>
                            <input type="datetime-local" name="absensi_start" class="form-control" value="{{ old('absensi_start', $event->absensi_start ? $event->absensi_start->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tutup Absensi</label>
                            <input type="datetime-local" name="absensi_end" class="form-control" value="{{ old('absensi_end', $event->absensi_end ? $event->absensi_end->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3 text-success"><i class="fas fa-graduation-cap me-1"></i> Instrumen & Syarat Kelulusan</h5>

                        <div class="mb-3 p-3 bg-white rounded border">
                            <label class="form-label font-weight-bold d-block mb-2">Instrumen yang Dikeluarkan ke Peserta:</label>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="has_pretest" id="hasPretest" value="1" {{ old('has_pretest', $event->has_pretest ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-bold" for="hasPretest">Keluarkan Ujian Pretest</label>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="has_posttest" id="hasPosttest" value="1" {{ old('has_posttest', $event->has_posttest ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-bold" for="hasPosttest">Keluarkan Ujian Posttest</label>
                            </div>

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="has_evaluasi" id="hasEvaluasi" value="1" {{ old('has_evaluasi', $event->has_evaluasi ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-bold" for="hasEvaluasi">Keluarkan Kuesioner Evaluasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Passing Grade (Nilai Minimal)</label>
                            <input type="number" name="passing_grade" class="form-control" value="{{ old('passing_grade', $event->passing_grade) }}" min="0" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Ujian Posttest</label>
                            <input type="text" name="posttest_password" class="form-control" value="{{ old('posttest_password', $event->posttest_password) }}" placeholder="Kosongkan jika tanpa PIN">
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-bold" for="isActive">Publikasikan Event</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-sm bg-gradient-primary w-100 py-2 font-weight-bold">
                    <i class="fas fa-sync-alt me-1"></i> Perbarui Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleCertificateInput() {
    const isAll = document.getElementById('cert_type_all').checked;
    const linkWrapper = document.getElementById('cert_link_wrapper');
    const uniqueInfo = document.getElementById('cert_unique_info');

    if (isAll) {
        linkWrapper.style.display = 'block';
        uniqueInfo.style.display = 'none';
    } else {
        linkWrapper.style.display = 'none';
        uniqueInfo.style.display = 'block';
    }
}

function toggleAbsensiInput() {
    const isLink = document.getElementById('absensi_type_link').checked;
    const linkWrapper = document.getElementById('absensi_link_wrapper');
    linkWrapper.style.display = isLink ? 'block' : 'none';
}
</script>
@endsection