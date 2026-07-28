@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-container--bootstrap-5 .select2-selection { border-radius: 0.5rem; padding: 0.5rem; height: auto; }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Event'])
    <div class="container-fluid py-4">
        @if ($errors->any())
            <div class="alert alert-danger text-white" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success text-white" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger text-white" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if(auth()->user()->role == 'admin')
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.events.create') }}" class="btn btn-success btn-sm">Buat Event Baru</a>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Manajemen Event & Pelatihan</h6>
                            <p class="text-muted text-xs mb-0">Kelola kegiatan pembinaan, sosialisasi, dan pelatihan internal.</p>
                        </div>
                        {{-- HANYA ADMIN yang bisa tambah event --}}
                        @if(auth()->user()->role == 'admin')
                            <a type="button" class="btn btn-primary btn-sm" href="{{ route('admin.events.create') }}">
                                <i class="fas fa-plus me-2"></i> Tambah Event
                            </a>
                        @endif
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="event-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Event</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jadwal Pelaksanaan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Peserta</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($events as $event)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm text-wrap">{{ $event->title }}</h6>
                                                    <p class="text-xs text-secondary mb-0 text-wrap">{{ Str::limit($event->deskripsi, 40) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-sm bg-gradient-info text-capitalize">{{ $event->category }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">
                                                {{ $event->start_at ? $event->start_at->format('d M Y, H:i') : '-' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs font-weight-bold">{{ $event->registrations_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($event->is_active)
                                                <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center">
                                                @if(auth()->user()->role == 'admin')
                                                    <a href="{{ route('admin.events.rekap', $event->id) }}" class="btn btn-outline-success btn-sm me-2 mb-0" title="Rekap Nilai">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-outline-primary btn-sm me-2 mb-0" title="Detail & Soal">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning btn-sm me-2 mb-0">
                                                        Edit
                                        </a>
                                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini?')" class="mb-0">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-info">View Only</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                        </tbody>
                    </table>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Mencegah re-inisialisasi DataTables
        if ($.fn.DataTable.isDataTable('#event-table')) {
            $('#event-table').DataTable().destroy();
        }
        $('#event-table').DataTable({
            paging: true,
            searching: true,
            info: true
        });

        $('.select2-modal').each(function() {
            $(this).select2({
                dropdownParent: $(this).closest('.modal'),
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    });
</script>
@endpush
@endsection