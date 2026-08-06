@extends('layouts.app')

@section('content')
@php
    $isEdit = isset($question);
    $actionUrl = $isEdit 
        ? route('admin.events.questions.update', [$event->id, $question->id]) 
        : route('admin.events.questions.store', $event->id);
    $selectedTypes = old('type', $isEdit ? [$question->type] : []);
    $selectedTypes = is_array($selectedTypes) ? $selectedTypes : [$selectedTypes];
@endphp

<div class="container-fluid py-4">
    <!-- Header Navigasi -->
    <div class="mb-4">
        <a href="{{ route('admin.events.show', $event->id) }}" class="text-decoration-none text-secondary text-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Event
        </a>
        <h3 class="font-weight-bold mt-2">{{ $isEdit ? 'Edit Soal Ujian' : 'Tambah Soal Ujian Baru' }}</h3>
        <p class="text-secondary text-sm mb-0">Event: <strong>{{ $event->title }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ $actionUrl }}" method="POST">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <!-- 1. Kategori / Tipe Ujian -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold font-weight-bold d-block mb-2">
                                Jenis Ujian <span class="text-danger">*</span>
                            </label>
                            
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('type') is-invalid @enderror"
                                        type="checkbox"
                                        name="type[]"
                                        id="typePretest"
                                        value="pretest" 
                                        {{ in_array('pretest', $selectedTypes) ? 'checked' : '' }}
                                        >
                                    <label class="form-check-label font-weight-bold text-dark" for="typePretest">
                                        Pretest <span class="text-muted font-weight-normal">(Awal Pelatihan)</span>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input @error('type') is-invalid @enderror"
                                        type="checkbox"
                                        name="type[]"
                                        id="typePosttest"
                                        value="posttest" 
                                        {{ in_array('posttest', $selectedTypes) ? 'checked' : '' }}
                                        >
                                    <label class="form-check-label font-weight-bold text-dark" for="typePosttest">
                                        Posttest <span class="text-muted font-weight-normal">(Akhir Pelatihan)</span>
                                    </label>
                                </div>
                            </div>

                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 2. Teks Pertanyaan -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold font-weight-bold">Butir Pertanyaan / Soal <span class="text-danger">*</span></label>
                            <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" rows="4" required placeholder="Tuliskan butir pertanyaan di sini...">{{ old('question_text', $question->question_text ?? '') }}</textarea>
                            @error('question_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">

                        <!-- 3. Pilihan Ganda (A, B, C, D, E) -->
                        <h6 class="font-weight-bold mb-3 text-dark"><i class="fas fa-list-ul me-1 text-success"></i> Pilihan Ganda</h6>
                        
                        <div class="row g-3 mb-4">
                            @foreach(['a', 'b', 'c', 'd', 'e'] as $optKey)
                                <div class="col-md-6">
                                    <label class="form-label small font-weight-bold">
                                        Pilihan {{ strtoupper($optKey) }} 
                                        @if(in_array($optKey, ['a', 'b'])) <span class="text-danger">*</span> @endif
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light font-weight-bold text-dark">{{ strtoupper($optKey) }}</span>
                                        <input type="text" 
                                               name="options[{{ $optKey }}]" 
                                               class="form-control @error("options.{$optKey}") is-invalid @enderror" 
                                               value="{{ old("options.{$optKey}", $question->options[$optKey] ?? '') }}" 
                                               {{ in_array($optKey, ['a', 'b']) ? 'required' : '' }} 
                                               placeholder="Opsi {{ strtoupper($optKey) }} {{ in_array($optKey, ['c', 'd', 'e']) ? '(Opsional)' : '' }}">
                                    </div>
                                    @error("options.{$optKey}") <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- 4. Kunci Jawaban Benar -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold font-weight-bold">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                            <select name="correct_answer" class="form-select @error('correct_answer') is-invalid @enderror" required>
                                <option value="" disabled {{ old('correct_answer', $question->correct_answer ?? '') ? '' : 'selected' }}>-- Pilih Kunci Jawaban --</option>
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $optKey)
                                    <option value="{{ $optKey }}" {{ old('correct_answer', $question->correct_answer ?? '') == $optKey ? 'selected' : '' }}>
                                        {{ strtoupper($optKey) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('correct_answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-outline-secondary px-4">Batal</a>
                            <button type="submit" class="btn btn-sm bg-gradient-success px-4 font-weight-bold">
                                <i class="fas {{ $isEdit ? 'fa-sync-alt' : 'fa-save' }} me-1"></i> 
                                {{ $isEdit ? 'Perbarui Soal' : 'Simpan Soal' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Petunjuk Ringkas -->
        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold mb-3 text-success"><i class="fas fa-info-circle me-1"></i> Informasi Pengisian</h6>
                    <ul class="text-secondary text-sm ps-3 mb-0">
                        <li class="mb-2"><strong>Pretest:</strong> Ujian awal untuk mengukur pemahaman awal peserta sebelum materi disampaikan.</li>
                        <li class="mb-2"><strong>Posttest:</strong> Ujian akhir yang menentukan kelulusan peserta (bobot 50% terhadap nilai akhir).</li>
                        <li class="mb-2">Pilihan A dan B **wajib diisi**, sedangkan C, D, dan E opsional.</li>
                        <li>Pastikan kunci jawaban benar telah terpilih dengan tepat sebelum menyimpan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection