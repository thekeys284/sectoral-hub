@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Tambah Event Baru'])
<div class="container-fluid py-2">
    <div class="row g-0">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <a href="{{ route('admin.events.index') }}" class="text-decoration-none text-secondary text-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Event
                </a>
                <h3 class="font-weight-bold mt-1 mb-0">Tambah Event Baru</h3>
                <p class="text-secondary text-sm mb-0"></p>
            </div>
        </div>
    </div>
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Informasi Utama</h5>
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Judul Event / Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Contoh: Pelatihan Analisis Spasial QGIS 2026">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Kategori <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="pelatihan" {{ old('category') == 'pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                                    <option value="pembinaan" {{ old('category') == 'pembinaan' ? 'selected' : '' }}>Pembinaan</option>
                                    <option value="sosialisasi" {{ old('category') == 'sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                                    <option value="rapat" {{ old('category') == 'rapat' ? 'selected' : '' }}>Rapat</option>
                                </select>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Lokasi / Keterangan Tempat</label>
                                <input type="text" name="lokasi_event" class="form-control" value="{{ old('lokasi_event') }}" placeholder="Contoh: Ruang Vicon / Online via Zoom">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Deskripsi Pelatihan</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tuliskan silabus atau informasi singkat kegiatan ini...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Tautan Streaming & Materi</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Link Zoom / Virtual Meeting</label>
                                <input type="url" name="meeting_link" class="form-control @error('meeting_link') is-invalid @enderror" value="{{ old('meeting_link') }}" placeholder="https://zoom.us/j/...">
                                @error('meeting_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Link YouTube</label>
                                <input type="url" name="youtube_link" class="form-control @error('youtube_link') is-invalid @enderror" value="{{ old('youtube_link') }}" placeholder="https://youtube.com/...">
                                @error('youtube_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Link Materi</label>
                                <input type="url" name="link_materi" class="form-control @error('link_materi') is-invalid @enderror" value="{{ old('link_materi') }}" placeholder="https://drive.google.com">
                                @error('link_materi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3">Banner & Virtual Background</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Image Banner</label>
                                <input type="file" name="image_banner" class="form-control @error('image_banner') is-invalid @enderror" accept="image/*">
                                <small class="text-muted d-block mt-1">Format: JPG/PNG/WEBP, maks 2MB.</small>
                                @error('image_banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Virtual Background</label>
                                <input type="file" name="virtual_bg" class="form-control @error('virtual_bg') is-invalid @enderror" accept="image/*">
                                <small class="text-muted d-block mt-1">Format: JPG/PNG/WEBP, maks 2MB.</small>
                                @error('virtual_bg')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <input type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror" value="{{ old('start_at') }}">
                            @error('start_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu Selesai Event</label>
                            <input type="datetime-local" name="end_at" class="form-control @error('end_at') is-invalid @enderror" value="{{ old('end_at') }}">
                            @error('end_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label class="form-label">Buka Absensi Mandiri</label>
                            <input type="datetime-local" name="absensi_start" class="form-control @error('absensi_start') is-invalid @enderror" value="{{ old('absensi_start') }}">
                            @error('absensi_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tutup Absensi Mandiri</label>
                            <input type="datetime-local" name="absensi_end" class="form-control @error('absensi_end') is-invalid @enderror" value="{{ old('absensi_end') }}">
                            @error('absensi_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold mb-3 text-success"><i class="fas fa-graduation-cap me-1"></i> Syarat Kelulusan</h5>

                        <div class="mb-3">
                            <label class="form-label">Passing Grade (Nilai Minimal)</label>
                            <input type="number" name="passing_grade" class="form-control @error('passing_grade') is-invalid @enderror" value="{{ old('passing_grade', 70) }}" min="0" max="100">
                            <small class="text-muted">Syarat nilai akhir untuk klaim sertifikat.</small>
                            @error('passing_grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Ujian Posttest</label>
                            <input type="text" name="posttest_password" class="form-control @error('posttest_password') is-invalid @enderror" value="{{ old('posttest_password') }}" placeholder="Misal: POSTTEST2026">
                            <small class="text-muted">Kosongkan jika tidak memakai PIN akses.</small>
                        </div>

                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label font-weight-bold" for="isActive">Publikasikan Event</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-sm bg-gradient-primary w-100 py-2 font-weight-bold">
                    <i class="fas fa-save me-1"></i> Simpan Event
                </button>
            </div>
        </div>
    </form>
</div>
@endsection