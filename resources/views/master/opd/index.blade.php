@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen OPD'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Manajemen OPD</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahOpd">
                            <i class="fas fa-plus me-2"></i>Tambah OPD
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ni ni-cloud-upload-95 me-1"></i> Import Excel
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="opd-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dinas</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Alamat</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($opds as $opd)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    {{-- Perbaikan Path Logo (Sesuai folder public/logos) --}}
                                                    <img src="{{ ($opd->logo_path && file_exists(public_path($opd->logo_path))) 
                                                                ? asset($opd->logo_path) 
                                                                : 'https://ui-avatars.com/api/?name=' . urlencode($opd->name) . '&background=random' }}"
                                                        class="avatar avatar-sm me-3" 
                                                        style="object-fit: cover;"
                                                        alt="opd_logo">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $opd->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Pembina: {{ $opd->pembina->name ?? 'Belum ada' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-xs font-weight-bold">{{ Str::limit($opd->alamat, 40) }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $opd->email }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <button type="button" class="btn btn-warning btn-sm me-2 mb-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditOpd{{ $opd->id }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.opd.destroy', $opd->id) }}" method="POST" 
                                                    onsubmit="return confirm('Yakin ingin menghapus OPD ini?')" class="mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm mb-0">
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

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambahOpd" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.opd.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah OPD Baru</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama OPD</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email OPD</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Pembina (BPS)</label>
                            <select name="pembina_id" class="form-control">
                                <option value="">-- Pilih Pembina --</option>
                                {{-- PERBAIKAN: Gunakan $users, bukan $opds --}}
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Logo OPD</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    @foreach($opds as $opd)
    <div class="modal fade" id="modalEditOpd{{ $opd->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.opd.update', $opd->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit OPD: {{ $opd->name }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama OPD</label>
                            <input type="text" name="name" value="{{ $opd->name }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ $opd->email }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required>{{ $opd->alamat }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Pembina BPS</label>
                            <select name="pembina_id" class="form-control">
                                <option value="">-- Pilih Pembina --</option>
                                {{-- PERBAIKAN: Gunakan $users --}}
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $opd->pembina_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ganti Logo (Opsional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

        <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.opd.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import OPD</h5>
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

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <script>
        $(document).ready(function() {
            $('#opd-table').DataTable({
                "language": {
                    "search": "Cari OPD:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": { "previous": "<", "next": ">" }
                }
            });
        });
    </script>
@endsection