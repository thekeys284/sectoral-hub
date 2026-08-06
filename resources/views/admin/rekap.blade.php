@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Rekap Penilaian Peserta'])
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('admin.events.index') }}" class="text-decoration-none text-secondary text-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Event
                </a>
                <h3 class="font-weight-bold mt-1 mb-0">Rekap Penilaian Peserta</h3>
                <p class="text-secondary text-sm mb-0">{{ $event->title }}</p>
            </div>
        </div>

        <div class="alert alert-info border-0 shadow-sm mb-2 py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 text-info"></i>
                <div>
                    <h6 class="font-weight-bold mb-0 text-sm">Rumus Penilaian Akhir (Passing Grade: {{ $event->passing_grade }})</h6>
                    <small class="text-xs">Nilai Akhir = (Kehadiran × 20%) + (Pretest × 25%) + (Posttest × 50%) + (Evaluasi × 5%)</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Tabel Rekap Penilaian Peserta</h6>
                            <p class="text-sm mb-0">{{ $event->title }}</p>
                        </div>
                        <div>
                            <button id="btn-export-peserta" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-users me-1"></i> Download Daftar Peserta
                            </button>
                            <button id="btn-export-lms" class="btn btn-sm bg-gradient-info">
                                <i class="fas fa-graduation-cap me-2"></i> Download Nilai LMS
                            </button>
                            <button id="btn-export-excel" class="btn btn-sm bg-gradient-success">
                                <i class="fas fa-file-excel me-2"></i> Export Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive px-3 pt-2 pb-3">
                            <table class="table align-items-center mb-0" id="rekap-table">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>

                                        {{-- Kolom tersembunyi khusus untuk export LMS (tidak ditampilkan di tabel layar) --}}
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIP</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>

                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Peserta</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kehadiran (20%)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pretest (25%)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Posttest (50%)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Evaluasi (5%)</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nilai Akhir</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Kelulusan</th>

                                        {{-- Kolom tersembunyi: status lulus dalam bentuk angka 1/0 untuk export LMS --}}
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">is_lulus</th>

                                        {{-- Kolom tersembunyi khusus untuk export Daftar Peserta --}}
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Satuan Kerja</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Alamat</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipe Peserta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapData as $index => $item)
                                        <tr>
                                            <td class="text-xs">{{ $index + 1 }}</td>

                                            {{-- data untuk export LMS saja --}}
                                            <td class="text-xs">{{ $item['nip'] ?? '-' }}</td>
                                            <td class="text-xs">{{ $item['name'] }}</td>

                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-xs">{{ $item['name'] }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $item['username'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($item['status_absen'])
                                                    <span class="badge badge-sm bg-gradient-success">Hadir (100)</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-danger">Tidak (0)</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-xs font-weight-bold">{{ $item['score_pretest'] }}</td>
                                            <td class="text-center text-xs font-weight-bold">{{ $item['score_posttest'] }}</td>
                                            <td class="text-center">
                                                @if($item['sudah_evaluasi'])
                                                    <span class="badge badge-sm bg-gradient-info">Sudah (100)</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-warning">Belum (0)</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="text-xs font-weight-bold">{{ $item['nilai_akhir'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($item['is_lulus'])
                                                    <span class="badge badge-sm bg-gradient-success">
                                                        <i class="fas fa-check-circle me-1"></i> LULUS
                                                    </span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-danger">
                                                        <i class="fas fa-times-circle me-1"></i> TIDAK LULUS
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- data untuk export LMS saja: 1 = lulus, 0 = tidak lulus --}}
                                            <td class="text-xs">{{ $item['is_lulus'] ? 1 : 0 }}</td>

                                            {{-- data untuk export Daftar Peserta saja --}}
                                            @php
                                                $opdName = optional($item['opd'] ?? null)->name ?? '-';
                                                $opdAlamat = optional($item['opd'] ?? null)->alamat ?? '-';
                                                $tipePeserta = ($opdName === 'BPS Provinsi Jawa Timur') ? 'Internal' : 'Eksternal';
                                            @endphp
                                            <td class="text-xs">{{ $opdName }}</td>
                                            <td class="text-xs">{{ $opdAlamat }}</td>
                                            <td class="text-xs">{{ $tipePeserta }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-5 text-secondary text-sm">
                                                Belum ada peserta yang mendaftar pada pelatihan ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    /* Perkecil font tabel supaya semua kolom muat tanpa scroll kanan-kiri */
    #rekap-table th, #rekap-table td {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        white-space: nowrap;
    }
</style>
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#rekap-table').DataTable({
            pageLength: 25,
            language: {
                search: "Cari Peserta:",
                paginate: { previous: "<", next: ">" }
            },
            dom: 'lBrtip', // 'B' mengaktifkan fungsionalitas Buttons

            // Kolom index (urutan sesuai <th> di atas, dimulai dari 0):
            // 0:No 1:NIP(hidden) 2:Nama bersih(hidden) 3:Nama Peserta 4:Kehadiran
            // 5:Pretest 6:Posttest 7:Evaluasi 8:Nilai Akhir 9:Status Kelulusan 10:is_lulus(hidden)
            // 11:Satuan Kerja(hidden) 12:Alamat(hidden) 13:Tipe Peserta(hidden)
            columnDefs: [
                { targets: [1, 2, 10, 11, 12, 13], visible: false } // sembunyikan dari tampilan tabel di layar
            ],

            buttons: [
                {
                    // Tombol "Export Excel": semua kolom yang tampil di layar (otomatis skip kolom hidden)
                    extend: 'excelHtml5',
                    filename: 'Rekap Penilaian - {{ $event->title }}', // nama file saat di-download
                    title: '', // string kosong -> tidak ada baris judul di dalam sheet
                    className: 'buttons-excel-full d-none'
                },
                {
                    // Tombol "Download Nilai LMS": hanya No, NIP, Nama, Nilai, is_lulus
                    extend: 'excelHtml5',
                    filename: 'Nilai LMS - {{ $event->title }}', // nama file saat di-download
                    title: '', // string kosong -> tidak ada baris judul di dalam sheet
                    className: 'buttons-excel-lms d-none',
                    exportOptions: {
                        columns: [0, 1, 2, 8, 10] // No, NIP, Nama, Nilai Akhir, is_lulus
                    }
                },
                {
                    // Tombol "Download Daftar Peserta": No, NIP, Nama, Satuan Kerja, Alamat, Tipe Peserta
                    extend: 'excelHtml5',
                    filename: 'Daftar Peserta - {{ $event->title }}', // nama file saat di-download
                    title: '', // string kosong -> tidak ada baris judul di dalam sheet
                    className: 'buttons-excel-peserta d-none',
                    exportOptions: {
                        columns: [0, 1, 2, 11, 12, 13] // No, NIP, Nama, Satuan Kerja, Alamat, Tipe Peserta
                    }
                }
            ]
        });

        // Tombol "Export Excel" di header memicu export lengkap
        $('#btn-export-excel').on('click', function() {
            table.button('.buttons-excel-full').trigger();
        });

        // Tombol "Download Nilai LMS" di header memicu export format LMS
        $('#btn-export-lms').on('click', function() {
            table.button('.buttons-excel-lms').trigger();
        });

        // Tombol "Download Daftar Peserta" di header memicu export daftar peserta
        $('#btn-export-peserta').on('click', function() {
            table.button('.buttons-excel-peserta').trigger();
        });
    });
</script>
@endpush