@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen SDSN'])
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
                        <h6>Manajemen SDSN</h6>
                        <a href="{{ route('master.sdsn.create') }}" class="btn btn-primary btn-sm">Tambah SDSN</a>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ni ni-cloud-upload-95 me-1"></i> Import Excel
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="sdsn-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode SDSN</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-wrap">Nama Data</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Konsep</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-wrap">Definisi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sdsn as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item->kode_sdsn }}</h6>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle text-sm">
                                            <span class="font-weight-bold">
                                            {{ $item->nama_data }}
                                            </span>
                                        </td>
                                        
                                        <td class="align-middle text-center text-sm">
                                            <span>{{ $item->konsep }}</span>
                                        </td>

                                        <td class="align-middle text-center text-sm text-wrap">
                                            <span>{{ Str::limit($item->definisi, 50) }}</span>
                                        </td>

                                        <td class="align-middle">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('master.sdsn.edit', $item->id) }}" 
                                                   class="btn btn-warning font-weight-bold text-xs me-2 mb-0">
                                                    Edit
                                                </a>
                                                <form action="{{ route('master.sdsn.destroy', $item->id) }}" method="POST" 
                                                    onsubmit="return confirm('Yakin ingin menghapus sdsn ini?')" class="mb-0">
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
            <form action="{{ route('master.sdsn.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import sdsn</h5>
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
            $('#sdsn-table').DataTable({
                "language": {
                    "search": "Cari sdsn:",
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