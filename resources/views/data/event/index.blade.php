@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Daftar Event'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Tabel Event</h6>
                        {{-- HANYA ADMIN yang bisa tambah event --}}
                        @if(auth()->user()->role == 'admin')
                            <a href="{{ route('event.create') }}" class="btn btn-primary btn-sm">Tambah Event Baru</a>
                        @endif
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-4">
                            <table class="table align-items-center mb-0" id="event-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lokasi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset($item->image_banner ?? 'img/no-image.png') }}" class="avatar avatar-sm me-3" alt="event">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item->title }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $item->category }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-xs font-weight-bold">{{ Str::limit($item->lokasi_event, 40) }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ date('d/m/Y', strtotime($item->start_at)) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center">
                                                @if(auth()->user()->role == 'admin')
                                                    <a href="{{ route('event.edit', $item->id) }}" class="btn btn-warning btn-sm me-2 mb-0">Edit</a>
                                                    <form action="{{ route('event.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?')" class="mb-0">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm mb-0">Hapus</button>
                                                    </form>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-info">View Only</span>
                                                @endif
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

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script>
        $(document).ready(function() {
            $('#event-table').DataTable();
        });
    </script>
@endsection