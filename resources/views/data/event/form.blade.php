@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $event->id ? 'Edit Event' : 'Tambah Event'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Form {{ $event->id ? 'Edit' : 'Tambah' }} Event</h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger text-white" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ $event->id ? route('event.update', $event->id) : route('event.store') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($event->id) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Judul Event</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="d-block">Status Aktif</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" 
                                        id="statusSwitch" value="1"
                                        {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold" for="statusSwitch">
                                        Geser untuk mengaktifkan event
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_at" class="form-control" value="{{ old('start_at', $event->start_at ? date('Y-m-d', strtotime($event->start_at)) : '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="end_at" class="form-control" value="{{ old('end_at', $event->end_at ? date('Y-m-d', strtotime($event->end_at)) : '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lokasi Event</label>
                                <input type="text" name="lokasi_event" class="form-control" value="{{ old('lokasi_event', $event->lokasi_event) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category" class="form-control" required>
                                    <option value="" disabled {{ old('category', $event->category) == '' ? 'selected' : '' }}>-- Pilih Kategori --</option>
                                    <option value="Sosialisasi" {{ old('category', $event->category) == 'Sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                                    <option value="Rapat" {{ old('category', $event->category) == 'Rapat' ? 'selected' : '' }}>Rapat</option>
                                    <option value="Pelatihan" {{ old('category', $event->category) == 'Pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Banner Event (JPG/PNG)</label>
                                <input type="file" name="image_banner" class="form-control" id="inputBanner" accept="image/*">
                                
                                <div class="mt-3">
                                    {{-- Preview Image --}}
                                    <img id="previewBanner" 
                                        src="{{ $event->image_banner ? asset($event->image_banner) : '#' }}" 
                                        alt="Preview Banner" 
                                        class="img-fluid border-radius-lg shadow {{ $event->image_banner ? '' : 'd-none' }}" 
                                        style="max-height: 200px;">
                                </div>
                                
                                @if($event->image_banner)
                                    <small class="text-muted d-block mt-2">File saat ini: {{ basename($event->image_banner) }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Virtual Background (JPG/PNG)</label>
                                <input type="file" name="virtual_bg" class="form-control" id="inputVbg" accept="image/*">
                                
                                <div class="mt-3">
                                    {{-- Preview Image --}}
                                    <img id="previewVbg" 
                                        src="{{ $event->virtual_bg ? asset($event->virtual_bg) : '#' }}" 
                                        alt="Preview Virtual Background" 
                                        class="img-fluid border-radius-lg shadow {{ $event->virtual_bg ? '' : 'd-none' }}" 
                                        style="max-height: 200px;">
                                </div>

                                @if($event->virtual_bg)
                                    <small class="text-muted d-block mt-2">File saat ini: {{ basename($event->virtual_bg) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if(auth()->user()->role == 'admin')
                            <button type="submit" class="btn btn-primary">Simpan Data Event</button>
                        @endif
                        <a href="{{ route('event.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script>
    function readURL(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result);
                $(previewId).removeClass('d-none');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Trigger preview untuk Banner
    $("#inputBanner").change(function() {
        readURL(this, '#previewBanner');
    });

    // Trigger preview untuk Virtual Background
    $("#inputVbg").change(function() {
        readURL(this, '#previewVbg');
    });
</script>
@endpush