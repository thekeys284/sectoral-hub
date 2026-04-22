@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Metadata Kegiatan Statistik'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Daftar Metadata Kegiatan Statistik</h6>
                        {{-- TOMBOL TAMBAH DIHAPUS --}}
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="metadata-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Judul Kegiatan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Periode Submission</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($metadata as $item)
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
                                            <span class="badge badge-sm bg-gradient-info">{{ $item->periode_submission }}</span>
                                        </td>
                                        
                                        @php
                                            $cara = [
                                                'SUBMITTED' => ['label' => 'Disubmit', 'class' => 'bg-gradient-primary'],
                                                'APPROVED' => ['label' => 'Disetujui', 'class' => 'bg-gradient-success'],
                                                'REVISED' => ['label' => 'Telah Direvisi', 'class' => 'bg-gradient-info'],
                                                'REJECTED' => ['label' => 'Ditolak', 'class' => 'bg-gradient-danger'],
                                                'CORRECTION_REQUIRED' => ['label' => 'Perlu Koreksi', 'class' => 'bg-gradient-warning'],
                                            ];

                                            $statusKey = $item->status ?? 'unknown';
                                            $data = $cara[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'bg-gradient-secondary'];
                                        @endphp

                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm {{ $data['class'] }}">
                                                {{ $data['label'] }}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center">
                                            {{-- HANYA TOMBOL SHOW/DETAIL --}}
                                            <a href="{{ route('data.metadata.show', $item->id) }}" 
                                               class="btn btn-info btn-xs font-weight-bold mb-0">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
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

    {{-- DataTables Scripts Tetap Sama --}}
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <script>
        $(document).ready(function() {
            $('#metadata-table').DataTable({
                "language": {
                    "search": "Cari Metadata:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": { "previous": "<", "next": ">" }
                }
            });
        });
    </script>
@endsection