@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Rekomendasi Kegiatan Statistik'])
    <div class="container-fluid py-4">
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
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Daftar Rekomendasi Kegiatan Statistik</h6>
                        <a href="{{ route('admin.romantik.create') }}" class="btn btn-primary btn-sm">Tambah Romantik</a>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ni ni-cloud-upload-95 me-1"></i> Import Excel
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="romantik-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Kegiatan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahun Kegiatan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Rekomendasi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($romantiks as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item->judul_kegiatan }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $item->opd->name ?? 'Tidak Ada OPD' }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle text-sm">
                                            <span class="badge badge-sm bg-gradient-info">{{ $item->tahun_kegiatan }}</span>

                                        </td>
                                        
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm 
                                                {{ $item->status_rekomendasi == 'dibatalkan' ? 'bg-gradient-danger' : 
                                                ($item->status_rekomendasi == 'ditolak' ? 'bg-gradient-error' : 
                                                ($item->status_rekomendasi == 'layak' ? 'bg-gradient-success' : 'bg-gradient-info')) }}">
                                                
                                                {{ ucfirst($item->status_rekomendasi) }}
                                            </span>
                                        </td>

                                        <td class="align-middle">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('admin.romantik.edit', $item->id) }}" 
                                                   class="btn btn-warning font-weight-bold text-xs me-2 mb-0">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.romantik.destroy', $item->id) }}" method="POST" 
                                                    onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')" class="mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger text-xs font-weight-bold mb-0">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
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
        <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.romantik.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Romantik</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file_excel" class="form-control" required>
                        </div>
                        <small class="text-muted">Pastikan kolom sesuai</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTables Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <script>
        $(document).ready(function() {
            $('#romantik-table').DataTable({
                "language": {
                    "search": "Cari Rekomendasi Statistik:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });
        });
    </script>
@endsection