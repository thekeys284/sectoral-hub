@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-container--bootstrap-5 .select2-selection { border-radius: 0.5rem; padding: 0.5rem; height: auto; }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Daftar Data'])
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
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Manajemen Daftar Data</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                            <i class="fas fa-plus me-2"></i>Tambah Data
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ni ni-cloud-upload-95 me-1"></i> Import Excel
                        </button>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="daftar-data-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Data</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Aliran Data</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Kebutuhan Data/Informasi Lainnya</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sifat</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($daftardata as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item->nama_data }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Dinas: {{ $item->opd->name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle"><span class="text-xs font-weight-bold">{{ $item->satuan }}</span></td>
                                        <td class="align-middle text-center"><span class="text-xs">{{ ucfirst($item->periode) }}</span></td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm 
                                                {{ $item->sifat_data == 'Terbuka' ? 'bg-gradient-success' : 
                                                ($item->sifat_data == 'Terbatas' ? 'bg-gradient-warning' : 
                                                'bg-gradient-secondary') }}">
                                                
                                                {{ ucfirst($item->sifat_data) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center">
                                                <button 
                                                    class="btn btn-warning btn-sm me-2 mb-0 btn-edit"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama_data }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit">
                                                    Edit
                                                </button>
                                                <form action="{{ route('data.daftardata.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
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
    </div>

    @php
        $units = ['Orang', 'Kejadian', 'Lembaga', 'Ormas/ LSM', 'Ormas', 'Parpol', 'Indeks', 'Persen', 'kegiatan', 'Proyek', 'Desa', 'dokumen', 'jiwa', 'buah', 'sekolah', 'unit', 'Data (Rp)', 'Rp', 'Rupiah', 'Peserta', 'Laporan', 'Paguyuban', 'Sanggar', 'UKM', 'Program', 'kajian/ judul', 'Inovasi', 'Kabupaten/ Kota', 'Perangkat Daerah', 'Peraturan Gubernur', 'Rilis', 'perkara', 'Poin', 'Daerah', 'Segmen', 'Usulan', 'Aset', 'Skor', 'Unit Pelaksana Teknis', 'MW', 'Pelanggan', 'RTM', 'MMSCFD', 'BOPD', 'ton', 'Budaya', 'Cagar Budaya', 'Desa Wisata', 'Kunjungan', 'Usaha', 'US Dolar', 'Data', 'Unit Usaha', 'Kg', 'Batang', 'Ha', 'M3', 'Ha, KK, SK', 'Ekor', 'RTP/ PP', 'Trip', 'Tip', 'Uji', 'Kelompok', 'daftar', 'Bayi', 'desa/ kelurahan', 'KLB', 'Ibu', 'Kasus', 'Balita', 'Puskesmas', 'Pasar', 'Aplikasi', 'kali', 'Mbps', 'konten', 'domain', 'putusan', 'pengaduan', 'informasi', 'permohonan', 'produk', 'Daftar Data', 'infografis', 'Insiden', 'Area', 'Komunitas', 'Koperasi', 'Milyar Rupiah', 'Institusi/ Non institusi', 'Usaha dan/ atau kegiatan', 'sampel', 'CO2e', 'Mg/ L', 'MPN/ 100 ml', 'ppm', 'industri', 'Pesantren', 'km', 'mm', 'BUMDesa', 'BumdesMA', 'KPM', 'Kerjasama', 'UEM', 'perizinan berusaha', 'Triliun Rupiah', 'Guru', 'Siswa', 'kendaraan', 'Meter', 'barang/ jasa', 'Rupiah/ kg', 'Judul', 'Perpustakaan', 'Ton/ liter', 'Rp/ Kg', 'Kuintal', 'Kawasan', 'lokasi', 'RT', 'Rp/ liter', 'Rp/ butir', 'Penerima Manfaat', 'perusahaan', 'Level', 'Hari', 'Permil', 'Layanan', 'Pasien', 'Pemeriksaan', 'Obat', 'TT', 'Pemeriksanaan', 'Pengunjung'];
        $sources = ['Internal', 'BPS Provinsi Jawa Timur', 'Kab/Kota', 'Perhimpunan Periset Indonesia', 'LPPD', 'LPTQ', 'DMI', 'KemenPANRB', 'Kementerian Dalam Negeri', 'BUMD Provinsi Jawa Timur', 'BPR Jatim Perseroda (Bank UMKM) dan BPD Jawa Timur (Bank Jatim)', 'Bakorwil Jawa Timur', '38 Kabupaten/Kota Provinsi Jawa Timur', 'LKPP', 'PLN', 'SKK MIGAS', 'Kementerian Kehutanan', 'Perum Perhutani dan UPT Kementerian Kehutanan', 'Perum Perhutani', 'Komite Olahraga Nasional Indonesia (KONI) Provinsi Jawa Timur dan National Paralympic Committee Indonesia (NPCI) Provinsi Jawa Timur', 'Melalui Aplikasi miliki KemenpanRB', 'ODS (Online Data System), Kemenkop', 'SIDT (System Informasi Data Tunggal), KemenUMKM', 'Kementerian LH', 'Kementerian LH, gabungan eksternal dan internal', 'Kementerian Desa', 'Data dari Prodeskel Kemendagri', 'Data dari Epdeskel Kemendagri', 'Data dari OM SPAN', 'Pengadilan Tinggi Agama', 'BPS', 'BKPM', 'Dapodik', 'Kementerian Perhubungan - BPTD', 'Perpusnas', 'Dinas Perpustakaan & Kearsipan Kabupaten/Kota', 'Kementerian Pertanian (Kementan)', 'Kabupaten/Kota', 'Kompilasi Kab/kota', 'KODAM - E RTLH', 'Kompilasi Kab/kota - SIKAWANKU', 'Data dari Laporan Dinsos Kab/Kota', 'Kementerian Ketenagakerjaan', 'BPKP'];
    @endphp

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambahData" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('data.daftardata.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5>Tambah Daftar Data Baru</h5></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nama Data</label>
                                <input type="text" name="nama_data" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Satuan</label>
                                <select name="satuan" class="form-control select2-modal" required>
                                    <option value="" disabled selected>-- Pilih Satuan --</option>
                                    @foreach($units as $unit) <option value="{{ $unit }}">{{ $unit }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>OPD Pemilik Data</label>
                                <select name="opd_id" class="form-control select2-modal" required>
                                    <option value="" disabled selected>-- Pilih OPD --</option>
                                    @foreach($opds as $opd) <option value="{{ $opd->id }}">{{ $opd->name }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Periode</label>
                                <select name="periode" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Periode --</option>
                                    <option value="hari">Hari</option><option value="bulan">Bulan</option><option value="triwulan">Triwulan</option><option value="semester">Semester</option><option value="tahun">Tahun</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kedalaman Kab/Kot?</label>
                                <select name="kedalaman_kabkot" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Opsi --</option>
                                <option value="Ya">Ya</option><option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Sifat Data</label>
                                <select name="sifat_data" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Sifat --</option>
                                    <option value="terbuka">Terbuka</option><option value="terbatas">Terbatas</option><option value="tertutup">Tertutup</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Sumber Data</label>
                                <select name="sumber_data" class="form-control select2-modal" required>
                                    <option value="" disabled selected>-- Pilih Sumber Data --</option>
                                    @foreach($sources as $source) <option value="{{ $source }}">{{ $source }}</option> @endforeach
                                </select>
                            </div>
                        <div class="col-md-12 mb-3">
                            <label>Kegiatan Statistik Terkait (Opsional)</label>
                            <select name="kegiatan_id" class="form-control select2-modal">
                                <option value="">-- Pilih Kegiatan (Opsional) --</option>
                                @foreach($kegiatans as $keg)
                                    <option value="{{ $keg->id }}">{{ $keg->nama_kegiatan }}</option>
                                @endforeach
                            </select>
                        </div>
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
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEdit" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5>Edit Data</h5>
                    </div>

                    <div class="modal-body">
                        <input type="text" name="nama_data" id="edit_nama" class="form-control">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('data.daftardata.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Daftar Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file_excel" class="form-control" required>
                        </div>
                        <small class="text-muted">Pastikan kolom sesuai: opd_id, nama_data, satuan, periode, kedalaman_kabkot, sifat_data, sumber_data, kegiatan_id.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload & Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#daftar-data-table').DataTable({
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