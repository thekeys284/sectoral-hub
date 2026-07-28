@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.events.index') }}" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Event
            </a>
            <h3 class="fw-bold mt-1 mb-0">Rekap Penilaian Peserta</h3>
            <p class="text-muted small mb-0">{{ $event->title }}</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-print me-1"></i> Cetak Laporan
        </button>
    </div>

    <!-- Card Ringkasan Bobot Penilaian -->
    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
            <div>
                <h6 class="fw-bold mb-1">Rumus Penilaian Akhir (Passing Grade: {{ $event->passing_grade }})</h6>
                <small>Nilai Akhir = (Kehadiran × 20%) + (Pretest × 25%) + (Posttest × 50%) + (Evaluasi × 5%)</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nama Peserta</th>
                            <th class="text-center">Kehadiran (20%)</th>
                            <th class="text-center">Pretest (25%)</th>
                            <th class="text-center">Posttest (50%)</th>
                            <th class="text-center">Evaluasi (5%)</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th class="text-center pe-4">Status Kelulusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapData as $index => $item)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                    <small class="text-muted">{{ $item['username'] }}</small>
                                </td>
                                <td class="text-center">
                                    @if($item['status_absen'])
                                        <span class="badge bg-success-subtle text-success">Hadir (100)</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Tidak (0)</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ $item['score_pretest'] }}</td>
                                <td class="text-center fw-semibold">{{ $item['score_posttest'] }}</td>
                                <td class="text-center">
                                    @if($item['sudah_evaluasi'])
                                        <span class="badge bg-info-subtle text-info">Sudah (100)</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Belum (0)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fs-6 fw-bold text-dark">{{ $item['nilai_akhir'] }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    @if($item['is_lulus'])
                                        <span class="badge bg-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> LULUS
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i> TIDAK LULUS
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
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
@endsection