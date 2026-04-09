@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen User'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-item-center">
                        <h6>Manajemen User</h6>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah User</a>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ni ni-cloud-upload-95 me-1"></i> Import Excel
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="user-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Dinas</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Role</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ ($user->profile_photo_path && file_exists(public_path('storage/' . $user->profile_photo_path))) 
                                                                ? asset('storage/' . $user->profile_photo_path) 
                                                                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                                        class="avatar avatar-sm me-3" 
                                                        style="object-fit: cover;"
                                                        alt="user_image">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                {{ $user->opd->nama_opd ?? 'Tidak Ada OPD' }}
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            {{-- Badge warna warni sesuai Role --}}
                                            @if($user->role == 'admin')
                                                <span class="badge badge-sm bg-gradient-danger">Admin</span>
                                            @elseif($user->role == 'pembina')
                                                <span class="badge badge-sm bg-gradient-info">Pembina</span>
                                            @elseif($user->role == 'walidata')
                                                <span class="badge badge-sm bg-gradient-success">Walidata</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Produsen</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                class="btn btn-warning font-weight-bold text-xs  me-2">
                                                    Edit
                                                </a>
                                                {{-- Form Hapus (PENTING: Harus pakai Form untuk Delete) --}}
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger text-xs font-weight-bold">
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
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file_excel" class="form-control" required>
                        </div>
                        <small class="text-muted">Pastikan kolom sesuai: opd_id, nama_data, satuan, periode, kedalaman_kabkot, sifat_data, sumber_data.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <script>
        $(document).ready(function() {
            $('#user-table').DataTable({
                "language": {
                    "search": "Cari User:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Halaman _PAGE_ dari _PAGES_",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });
        });
    </script>
@endsection
