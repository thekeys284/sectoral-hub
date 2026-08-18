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

        $isAdminOrPembina = in_array($activeRole, ['admin', 'pembina', 'superadmin']) 
                            || in_array('admin', $roles) 
                            || in_array('pembina', $roles);

        $opdList = collect();
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
            ->has('kegiatan')
            ->latest()
            ->get()
            ->groupBy('kegiatan_id');

        } else {
            // ADMIN / PEMBINA: Ambil list dinas untuk dropdown
            $opdList = Opd::orderBy('name', 'asc')->get();
            $selectedOpdId = $request->get('opd_id');

            // HANYA query database jika ada dinas yang dipilih di dropdown
            if ($selectedOpdId) {
                $daftardata = DaftarData::with([
                    'opd',
                    'kegiatan.metadataSubmissions.reviewer'
                ])
                ->where('opd_id', $selectedOpdId)
                ->whereNotNull('kegiatan_id')
                ->has('kegiatan')
                ->latest()
                ->get()
                ->groupBy('kegiatan_id');
            } else {
                // Jika belum pilih dinas -> Kosongkan koleksi agar server ringan
                $daftardata = collect();
            }
        }

        return view('pelaporan.metadata.index', compact('daftardata', 'opdList', 'isAdminOrPembina', 'selectedOpdId'));
    }

    public function submitLink(Request $request, $kegiatanId)
    {
        $request->validate([
            'tipe'     => 'required|in:kegiatan,variabel,indikator',
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
}