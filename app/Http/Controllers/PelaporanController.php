<?php

namespace App\Http\Controllers;
use App\Models\Kegiatan;
use App\Models\Opd;
use App\Models\Metadata;
use App\Models\Romantik;
use App\Models\DaftarData;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class PelaporanController extends Controller
{
    public function index()
    {
        // $daftardata = Daftardata::with(['opd', 'kegiatan'])->latest()->get();
        // return view('pelaporan.metadata.index', compact('daftardata'));
        // $opdId = Auth::user()->opd_id;
        // $daftardata = Daftardata::with(['opd', 'kegiatan'])
        //             ->where('opd_id', $opdId)
        //             ->whereNotNull('kegiatan_id')
        //             ->latest()
        //             ->get()
        //             ->groupBy('kegiatan_id');

        // return view('pelaporan.metadata.index', compact('daftardata'));
        $user = Auth::user();
    
        // Gunakan query builder dengan 'when' untuk kondisi dinamis
        $query = DaftarData::with(['opd', 'kegiatan'])
                    ->whereNotNull('kegiatan_id')
                    ->has('kegiatan'); // Memastikan data Kegiatan-nya benar-benar ada, bukan sekedar id-nya tidak null

        $roles = is_string($user->role) ? json_decode($user->role, true) ?? [$user->role] : (array) $user->role;
        $activeRole = session('active_role', $roles[0] ?? '');

        if ($activeRole === 'produsen') {
            $query->where('opd_id', $user->opd_id);}
        // } elseif ($activeRole === 'pembina') {
        //     $opdBinaanIds = \App\Models\Opd::where('pembina_id', $user->id)->pluck('id')->toArray();
        //     $query->whereIn('opd_id', $opdBinaanIds);
        // }

        $daftardata = $query->latest()
                            ->get() // Tetap gunakan paginate(10) agar rowspan aman
                            ->groupBy('kegiatan_id');

        return view('pelaporan.metadata.index', compact('daftardata'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        // Gunakan update dengan array agar ringkas
        $kegiatan->update($request->only([
            'link_metadata_kegiatan', 
            'link_metadata_variabel', 
            'link_metadata_indikator'
        ]));

        return back()->with('success', 'Link berhasil diperbarui!');
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'nama_data'    => 'required|string|max:255',
            'satuan'       => 'required|string',
            'kegiatan_id'  => 'required|exists:kegiatan,id',
            'opd_id'       => 'required|exists:opd,id',
            // ... field lainnya
        ]);

        // 2. Simpan ke database
        DaftarData::create($validated);

        // 3. Redirect
        return redirect()->route('pelaporan.metadata.index')->with('success', 'Data baru berhasil ditambahkan!');
    }
}
