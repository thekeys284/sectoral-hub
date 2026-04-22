@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Pelaporan Metadata'])
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header pb-0"><h6>Pelaporan Metadata</h6></div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-4">
                    <table class="table align-items-center mb-0 table-bordered">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Daftar Data</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Kegiatan Statistik</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Link Kegiatan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Link Variabel</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Link Indikator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftardata as $kegiatanId => $dataGroup)
                                @php $keg = $dataGroup->first()->kegiatan; @endphp
                                @foreach ($dataGroup as $index => $item)
                                <tr>
                                    <td><h6 class="mb-0 text-sm text-wrap">{{ $item->nama_data }}</h6></td>
                                    
                                    @if($index === 0)
                                    <td rowspan="{{ $dataGroup->count() }}" class="align-top bg-gray-50 border-end">
                                        <h6 class="text-xs mb-0 text-wrap">{{ $keg?->nama_kegiatan ?? 'Tanpa Kegiatan' }}</h6>
                                        <p class="text-xxs text-primary font-weight-bold text-wrap">{{ $item->opd->name ?? '-' }}</p>
                                    </td>
                                    
                                    {{-- Kolom Link Kegiatan --}}
                                    <td rowspan="{{ $dataGroup->count() }}" class="align-top">
                                        @if($keg?->link_metadata_kegiatan)
                                            <a href="{{ $keg->link_metadata_kegiatan }}" target="_blank" class="btn btn-sm btn-primary">Lihat</a>
                                            <br><button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKegiatan{{ $keg->id }}">Edit</button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tambahKegiatan{{ $keg->id }}">Isi Link</button>
                                        @endif
                                    </td>

                                    {{-- Kolom Link Variabel --}}
                                    <td rowspan="{{ $dataGroup->count() }}" class="align-top">
                                        @if($keg?->link_metadata_variabel)
                                            <a href="{{ $keg->link_metadata_variabel }}" target="_blank" class="btn btn-sm btn-primary">Lihat</a>
                                            <br><button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editVariabel{{ $keg->id }}">Edit</button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tambahVariabel{{ $keg->id }}">Isi Link</button>
                                        @endif
                                    </td>

                                    {{-- Kolom Link Indikator --}}
                                    <td rowspan="{{ $dataGroup->count() }}" class="align-top">
                                        @if($keg?->link_metadata_indikator)
                                            <a href="{{ $keg->link_metadata_indikator }}" target="_blank" class="btn btn-sm btn-success">Lihat</a>
                                            <br><button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editIndikator{{ $keg->id }}">Edit</button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tambahIndikator{{ $keg->id }}">Isi Link</button>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS SECTION --}}
    @foreach ($daftardata as $kegiatanId => $dataGroup)
        @php $keg = $dataGroup->first()->kegiatan; @endphp
        @if($keg)
            @foreach(['Kegiatan', 'Variabel', 'Indikator'] as $tipe)
                @php $field = 'link_metadata_'.strtolower($tipe); @endphp
                
                {{-- Modal Tambah --}}
                <div class="modal fade" id="tambah{{$tipe}}{{$keg->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog"><form action="{{ route('pelaporan.metadata.update', $keg->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">Isi Link {{ $tipe }}</h5></div>
                            <div class="modal-body"><input type="text" name="{{$field}}" class="form-control" placeholder="URL..." required></div>
                            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
                        </div>
                    </form></div>
                </div>

                {{-- Modal Edit --}}
                <div class="modal fade" id="edit{{$tipe}}{{$keg->id}}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog"><form action="{{ route('pelaporan.metadata.update', $keg->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">Edit Link {{ $tipe }}</h5></div>
                            <div class="modal-body"><input type="text" name="{{$field}}" value="{{ $keg->$field }}" class="form-control" required></div>
                            <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
                        </div>
                    </form></div>
                </div>
            @endforeach
        @endif
    @endforeach
@endsection