<?php

namespace App\Http\Controllers;
use App\Models\Kegiatan;
use App\Models\Opd;
use App\Models\Metadata;
use App\Models\Romantik;
use App\Models\Daftardata;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class PelaporanController extends Controller
{
    public function index()
    {
        // $daftardata = Daftardata::with(['opd', 'kegiatan'])->latest()->get();
        // return view('pelaporan.metadata.index', compact('daftardata'));
        $opdId = Auth::user()->opd_id;
        $daftardata = Daftardata::with(['opd', 'kegiatan'])
                    ->where('opd_id', $opdId)
                    ->whereNotNull('kegiatan_id') //nnti di comment, harus semua ada kegiatan idnya 
                    ->latest()
                    ->get()
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
        Daftardata::create($validated);

        // 3. Redirect
        return redirect()->route('pelaporan.metadata.index')->with('success', 'Data baru berhasil ditambahkan!');
    }
}
