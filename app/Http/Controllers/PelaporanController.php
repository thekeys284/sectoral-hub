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
            // PRODUSEN: Langsung load data dinas miliknya
            $selectedOpdId = $user->opd_id;
            
            $daftardata = DaftarData::with([
                'opd',
                'kegiatan.metadataSubmissions.reviewer'
            ])
            ->where('opd_id', $selectedOpdId)
            ->whereNotNull('kegiatan_id')
            ->whereHas('kegiatan', function ($q) use ($selectedOpdId) {
                $q->where('opd_id', $selectedOpdId);
            })
            ->latest()
            ->get()
            ->groupBy('kegiatan_id');

        } else {
            // ADMIN / PEMBINA: Ambil list dinas untuk dropdown (hanya OPD mandiri/induk)
            $opdList = Opd::whereNull('parent_id')->orderBy('name', 'asc')->get();
            $opdBinaan = Opd::where('pembina_id', $user->id)->whereNull('parent_id')->orderBy('name', 'asc')->get();
            $opdBinaanIds = $opdBinaan->pluck('id')->toArray();

            // Tentukan OPD yang dipilih
            if ($request->has('opd_id')) {
                // User memilih langsung dari dropdown (bisa OPD lain, OPD binaan, atau all_binaan)
                $selectedOpdId = $request->get('opd_id');
            } elseif ($isPembina && $opdBinaan->isNotEmpty()) {
                // Default untuk role pembina saat pertama buka menu:
                // Jika punya 1 OPD binaan -> langsung pilih ID OPD tersebut
                // Jika punya lebih dari 1 OPD binaan -> pilih 'all_binaan'
                $selectedOpdId = ($opdBinaan->count() > 1) ? 'all_binaan' : (string)$opdBinaan->first()->id;
            } else {
                $selectedOpdId = null;
            }

            // Jalankan query database
            if ($selectedOpdId === 'all_binaan' && !empty($opdBinaanIds)) {
                $daftardata = DaftarData::with([
                    'opd',
                    'kegiatan.metadataSubmissions.reviewer'
                ])
                ->whereIn('opd_id', $opdBinaanIds)
                ->whereNotNull('kegiatan_id')
                ->whereHas('kegiatan', function ($q) {
                    $q->whereColumn('kegiatan.opd_id', 'daftar_data.opd_id');
                })
                ->latest()
                ->get()
                ->groupBy('kegiatan_id');
            } elseif (!empty($selectedOpdId)) {
                $daftardata = DaftarData::with([
                    'opd',
                    'kegiatan.metadataSubmissions.reviewer'
                ])
                ->where('opd_id', $selectedOpdId)
                ->whereNotNull('kegiatan_id')
                ->whereHas('kegiatan', function ($q) use ($selectedOpdId) {
                    $q->where('opd_id', $selectedOpdId);
                })
                ->latest()
                ->get()
                ->groupBy('kegiatan_id');
            } else {
                // Jika belum pilih dinas -> Kosongkan koleksi agar server ringan
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
}