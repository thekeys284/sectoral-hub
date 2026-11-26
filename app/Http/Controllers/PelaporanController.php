<?php

namespace App\Http\Controllers;

use App\Models\DaftarData;
use App\Models\Kegiatan;
use App\Models\MetadataSubmission;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Parsing role
        $rawRole = $user->role;
        $roles = is_array($rawRole) ? $rawRole : (json_decode($rawRole, true) ?? [$rawRole]);
        $activeRole = session('active_role', $roles[0] ?? '');

        $isAdmin = ($activeRole === 'admin');

        $isAdminOrPembina = in_array($activeRole, ['admin', 'pembina', 'superadmin', 'walidata']) 
                            || in_array('admin', $roles) 
                            || in_array('pembina', $roles);

        $isPembina = ($activeRole === 'pembina') 
                     || (!in_array('admin', $roles) && in_array('pembina', $roles));

        $opdList = collect();
        $opdBinaan = collect();
        $selectedOpdId = null;

        if (!$isAdminOrPembina) {
            // PRODUSEN: Langsung load data dinas miliknya (tanpa correlated subquery EXISTS yang lambat)
            $selectedOpdId = $user->opd_id;
            $validKegiatanIds = Kegiatan::where('opd_id', $selectedOpdId)->pluck('id');

            if ($validKegiatanIds->isEmpty()) {
                $daftardata = collect();
            } else {
                $daftardata = DaftarData::select('id', 'nama_data', 'opd_id', 'kegiatan_id', 'created_at')
                    ->with([
                        'opd:id,name',
                        'kegiatan.metadataSubmissions.reviewer:id,name'
                    ])
                    ->where('opd_id', $selectedOpdId)
                    ->whereIn('kegiatan_id', $validKegiatanIds)
                    ->latest()
                    ->get()
                    ->groupBy('kegiatan_id');
            }

        } else {
            // ADMIN / PEMBINA: Ambil list dinas untuk dropdown (hanya kolom id & name agar ringan)
            $opdList = Opd::select('id', 'name', 'pembina_id')->whereNull('parent_id')->orderBy('name', 'asc')->get();
            $opdBinaan = $opdList->where('pembina_id', $user->id)->values();
            $opdBinaanIds = $opdBinaan->pluck('id')->toArray();

            // Tentukan OPD yang dipilih sesuai role & hak akses:
            // - Pembina: default ke 'all_binaan' (seluruh OPD binaannya)
            // - Admin / Walidata: default ke 'all' (tampilkan seluruh OPD yang punya kegiatan agar langsung bisa di-search semua)
            if ($request->filled('opd_id')) {
                $selectedOpdId = $request->get('opd_id');
            } elseif ($isPembina && $opdBinaan->isNotEmpty()) {
                $selectedOpdId = ($opdBinaan->count() > 1) ? 'all_binaan' : (string)$opdBinaan->first()->id;
            } else {
                $selectedOpdId = 'all';
            }

            // Jalankan query database tanpa correlated subquery whereHas yang lambat (tampilkan SEMUA data tanpa limit 10)
            if ($selectedOpdId === 'all') {
                $kegiatanMap = Kegiatan::pluck('opd_id', 'id');
                $daftardata = $kegiatanMap->isEmpty()
                    ? collect()
                    : DaftarData::select('id', 'nama_data', 'opd_id', 'kegiatan_id', 'created_at')
                        ->with([
                            'opd:id,name',
                            'kegiatan.metadataSubmissions.reviewer:id,name'
                        ])
                        ->whereIn('kegiatan_id', $kegiatanMap->keys())
                        ->latest()
                        ->get()
                        ->filter(fn($item) => isset($kegiatanMap[$item->kegiatan_id]) && (int) $kegiatanMap[$item->kegiatan_id] === (int) $item->opd_id)
                        ->groupBy('kegiatan_id');
            } elseif ($selectedOpdId === 'all_binaan' && !empty($opdBinaanIds)) {
                $kegiatanMap = Kegiatan::whereIn('opd_id', $opdBinaanIds)->pluck('opd_id', 'id');
                $daftardata = $kegiatanMap->isEmpty()
                    ? collect()
                    : DaftarData::select('id', 'nama_data', 'opd_id', 'kegiatan_id', 'created_at')
                        ->with([
                            'opd:id,name',
                            'kegiatan.metadataSubmissions.reviewer:id,name'
                        ])
                        ->whereIn('opd_id', $opdBinaanIds)
                        ->whereIn('kegiatan_id', $kegiatanMap->keys())
                        ->latest()
                        ->get()
                        ->filter(fn($item) => isset($kegiatanMap[$item->kegiatan_id]) && (int) $kegiatanMap[$item->kegiatan_id] === (int) $item->opd_id)
                        ->groupBy('kegiatan_id');
            } elseif (!empty($selectedOpdId)) {
                $validKegiatanIds = Kegiatan::where('opd_id', $selectedOpdId)->pluck('id');
                $daftardata = $validKegiatanIds->isEmpty()
                    ? collect()
                    : DaftarData::select('id', 'nama_data', 'opd_id', 'kegiatan_id', 'created_at')
                        ->with([
                            'opd:id,name',
                            'kegiatan.metadataSubmissions.reviewer:id,name'
                        ])
                        ->where('opd_id', $selectedOpdId)
                        ->whereIn('kegiatan_id', $validKegiatanIds)
                        ->latest()
                        ->get()
                        ->groupBy('kegiatan_id');
            } else {
                $daftardata = collect();
            }
        }

        return view('pelaporan.metadata.index', compact(
            'daftardata', 
            'opdList', 
            'isAdmin',
            'isAdminOrPembina', 
            'selectedOpdId', 
            'opdBinaan', 
            'isPembina'
        ));
    }

    public function submitLink(Request $request, $kegiatanId)
    {
        $request->validate([
            'tipe'     => 'required|in:kegiatan,variable,indikator',
            'link_url' => 'required|url',
        ]);

        MetadataSubmission::create([
            'kegiatan_id' => $kegiatanId,
            'tipe'        => $request->tipe,
            'link_url'    => $request->link_url,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Link metadata ' . ucfirst($request->tipe) . ' berhasil dikirim ke Pembina Data.');
    }

    public function reviewSubmission(Request $request, $submissionId)
    {
        $request->validate([
            'status'        => 'required|in:disetujui,butuh_perbaikan',
            'catatan_pembina' => 'nullable|string',
        ]);

        $submission = MetadataSubmission::findOrFail($submissionId);

        $submission->update([
            'status'        => $request->status,
            'catatan_pembina' => $request->catatan_pembina,
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
        ]);

        return redirect()->back()->with('success', 'Hasil telaah & catatan review berhasil disimpan.');
    }

    public function updateLink(Request $request, $submissionId)
    {
        $request->validate([
            'link_url' => 'required|url',
        ]);

        $submission = MetadataSubmission::findOrFail($submissionId);
        $submission->update([
            'link_url' => $request->link_url,
        ]);

        return redirect()->back()->with('success', 'Link metadata berhasil diperbarui.');
    }

    public function destroyLink($submissionId)
    {
        $submission = MetadataSubmission::findOrFail($submissionId);
        $submission->delete();

        return redirect()->back()->with('success', 'Link metadata berhasil dihapus.');
    }

    public function rekapitulasi(Request $request)
    {
        $opds = Opd::select('id', 'name', 'pembina_id')
            ->with('pembina:id,name,email')
            ->whereNull('parent_id')
            ->where('name', '!=', 'Badan Pusat Statistik Provinsi Jawa Timur')
            ->orderBy('name', 'asc')
            ->get();

        // Hitung jumlah daftar_data per (opd_id, kegiatan_id) langsung via SQL GROUP BY (tanpa load ribuan model DaftarData)
        $daftarDataCounts = \Illuminate\Support\Facades\DB::table('daftar_data')
            ->whereNotNull('kegiatan_id')
            ->selectRaw('opd_id, kegiatan_id, COUNT(*) as total')
            ->groupBy('opd_id', 'kegiatan_id')
            ->get();

        $countsByOpdAndKeg = [];
        $activeKegiatanIds = [];
        foreach ($daftarDataCounts as $row) {
            $countsByOpdAndKeg[$row->opd_id][$row->kegiatan_id] = (int) $row->total;
            $activeKegiatanIds[] = $row->kegiatan_id;
        }

        $kegiatansById = empty($activeKegiatanIds)
            ? collect()
            : Kegiatan::with(['metadataSubmissions.reviewer:id,name'])
                ->whereIn('id', array_unique($activeKegiatanIds))
                ->get()
                ->keyBy('id');

        $summary = [
            'total_opd'               => $opds->count(),
            'opd_belum_diperiksa'     => 0,
            'total_kegiatan'          => 0,
            'total_target_metadata'   => 0,
            'total_belum_upload'      => 0,
            'total_belum_ditelaah'    => 0,
            'total_butuh_perbaikan'   => 0,
            'total_disetujui'         => 0,
        ];

        $rekapOpd = [];
        $rekapKegiatan = [];

        foreach ($opds as $opd) {
            $opdKegMap = $countsByOpdAndKeg[$opd->id] ?? [];

            $validKegList = [];
            foreach ($opdKegMap as $kegiatanId => $dataCount) {
                $keg = $kegiatansById->get($kegiatanId);
                if ($keg && (int) $keg->opd_id === (int) $opd->id) {
                    $validKegList[] = ['keg' => $keg, 'count' => $dataCount];
                }
            }

            $totalKegiatan = count($validKegList);
            $totalTarget = $totalKegiatan * 3;
            $belumUpload = 0;
            $belumDitelaah = 0;
            $butuhPerbaikan = 0;
            $disetujui = 0;
            $latestActivityAt = null;

            foreach ($validKegList as $entry) {
                $keg = $entry['keg'];
                $jumlahData = $entry['count'];

                $tipeDetails = [];
                $kegHasPendingReview = false;

                foreach (['kegiatan', 'variable', 'indikator'] as $tipe) {
                    $submissions = $keg->metadataSubmissions
                        ->where('tipe', $tipe)
                        ->sortBy('created_at');
                    $latest = $submissions->last();
                    $revCount = $submissions->count();

                    if (!$latest) {
                        $belumUpload++;
                        $tipeDetails[$tipe] = [
                            'has_link'        => false,
                            'ada_telaah'      => false,
                            'status_terakhir' => 'belum_upload',
                            'status_label'    => 'Belum Upload Link',
                            'telaah_label'    => 'Belum Upload',
                            'catatan'         => null,
                            'reviewer'        => null,
                            'reviewed_at'     => null,
                            'submitted_at'    => null,
                            'link_url'        => null,
                            'rev_count'       => 0,
                        ];
                    } else {
                        if ($latest->created_at && (!$latestActivityAt || $latest->created_at->gt($latestActivityAt))) {
                            $latestActivityAt = $latest->created_at;
                        }
                        if ($latest->reviewed_at && (!$latestActivityAt || $latest->reviewed_at->gt($latestActivityAt))) {
                            $latestActivityAt = $latest->reviewed_at;
                        }

                        if ($latest->status === 'pending') {
                            $belumDitelaah++;
                            $kegHasPendingReview = true;
                            $tipeDetails[$tipe] = [
                                'has_link'        => true,
                                'ada_telaah'      => false,
                                'status_terakhir' => 'pending',
                                'status_label'    => 'Menunggu Pemeriksaan',
                                'telaah_label'    => 'Belum Ditelaah / Belum Ada Feedback',
                                'catatan'         => $latest->catatan_pembina,
                                'reviewer'        => null,
                                'reviewed_at'     => null,
                                'submitted_at'    => $latest->created_at,
                                'link_url'        => $latest->link_url,
                                'rev_count'       => $revCount,
                            ];
                        } elseif ($latest->status === 'butuh_perbaikan') {
                            $butuhPerbaikan++;
                            $tipeDetails[$tipe] = [
                                'has_link'        => true,
                                'ada_telaah'      => true,
                                'status_terakhir' => 'butuh_perbaikan',
                                'status_label'    => 'Perlu Perbaikan',
                                'telaah_label'    => 'Sudah Ditelaah (Ada Feedback)',
                                'catatan'         => $latest->catatan_pembina,
                                'reviewer'        => $latest->reviewer->name ?? 'Pembina Data',
                                'reviewed_at'     => $latest->reviewed_at,
                                'submitted_at'    => $latest->created_at,
                                'link_url'        => $latest->link_url,
                                'rev_count'       => $revCount,
                            ];
                        } else {
                            $disetujui++;
                            $tipeDetails[$tipe] = [
                                'has_link'        => true,
                                'ada_telaah'      => true,
                                'status_terakhir' => 'disetujui',
                                'status_label'    => 'Disetujui',
                                'telaah_label'    => 'Sudah Ditelaah (Disetujui)',
                                'catatan'         => $latest->catatan_pembina,
                                'reviewer'        => $latest->reviewer->name ?? 'Pembina Data',
                                'reviewed_at'     => $latest->reviewed_at,
                                'submitted_at'    => $latest->created_at,
                                'link_url'        => $latest->link_url,
                                'rev_count'       => $revCount,
                            ];
                        }
                    }
                }

                $rekapKegiatan[] = [
                    'opd'                    => $opd,
                    'pembina'                => $opd->pembina,
                    'kegiatan'               => $keg,
                    'jumlah_data'            => $jumlahData,
                    'tipe'                   => $tipeDetails,
                    'has_pending_review'     => $kegHasPendingReview,
                ];
            }

            $sudahUpload = $belumDitelaah + $butuhPerbaikan + $disetujui;
            $sudahDitelaah = $butuhPerbaikan + $disetujui;

            if ($totalKegiatan === 0) {
                $statusKey = 'belum_ada_kegiatan';
                $statusText = 'Belum Ada Kegiatan Terhubung';
                $statusBadge = 'bg-gradient-secondary';
            } elseif ($sudahUpload === 0) {
                $statusKey = 'belum_upload';
                $statusText = 'Dinas Belum Upload Link';
                $statusBadge = 'bg-gradient-secondary';
            } elseif ($belumDitelaah > 0) {
                $statusKey = 'belum_diperiksa';
                $statusText = "Ada {$belumDitelaah} Metadata Belum Diperiksa";
                $statusBadge = 'bg-gradient-danger';
                $summary['opd_belum_diperiksa']++;
            } elseif ($butuhPerbaikan > 0) {
                $statusKey = 'perlu_perbaikan';
                $statusText = 'Sudah Ditelaah — Perlu Perbaikan';
                $statusBadge = 'bg-gradient-warning text-dark';
            } elseif ($disetujui === $totalTarget && $totalTarget > 0) {
                $statusKey = 'disetujui_lengkap';
                $statusText = 'Selesai (Disetujui Semua)';
                $statusBadge = 'bg-gradient-success';
            } else {
                $statusKey = 'sebagian_disetujui';
                $statusText = 'Sudah Ditelaah (Sebagian Belum Upload)';
                $statusBadge = 'bg-gradient-info';
            }

            $summary['total_kegiatan'] += $totalKegiatan;
            $summary['total_target_metadata'] += $totalTarget;
            $summary['total_belum_upload'] += $belumUpload;
            $summary['total_belum_ditelaah'] += $belumDitelaah;
            $summary['total_butuh_perbaikan'] += $butuhPerbaikan;
            $summary['total_disetujui'] += $disetujui;

            $rekapOpd[] = [
                'opd'                => $opd,
                'pembina'            => $opd->pembina,
                'total_kegiatan'     => $totalKegiatan,
                'total_target'       => $totalTarget,
                'sudah_upload'       => $sudahUpload,
                'belum_upload'       => $belumUpload,
                'belum_ditelaah'     => $belumDitelaah,
                'sudah_ditelaah'     => $sudahDitelaah,
                'butuh_perbaikan'    => $butuhPerbaikan,
                'disetujui'          => $disetujui,
                'status_key'         => $statusKey,
                'status_text'        => $statusText,
                'status_badge'       => $statusBadge,
                'latest_activity_at' => $latestActivityAt,
            ];
        }

        // Urutkan agar OPD yang memiliki metadata belum diperiksa (belum_ditelaah > 0) tampil paling atas
        usort($rekapOpd, function ($a, $b) {
            if ($a['belum_ditelaah'] !== $b['belum_ditelaah']) {
                return $b['belum_ditelaah'] <=> $a['belum_ditelaah'];
            }
            if ($a['sudah_upload'] !== $b['sudah_upload']) {
                return $b['sudah_upload'] <=> $a['sudah_upload'];
            }
            return strcmp($a['opd']->name, $b['opd']->name);
        });

        return view('pelaporan.metadata.rekapitulasi', compact(
            'rekapOpd',
            'rekapKegiatan',
            'summary',
            'opds'
        ));
    }
}