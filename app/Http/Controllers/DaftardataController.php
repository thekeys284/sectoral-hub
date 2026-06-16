<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opd;
use App\Models\DaftarData;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DaftarDataImport;
use App\Models\Kegiatan;


class DaftardataController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query Builder (Bukan langsung memanggil ->get())
        $query = DaftarData::with(['opd', 'kegiatan']);

        // 2. Filter Pencarian Nama Data (Aktifkan kembali dengan benar)
        if ($request->filled('search')) {
            $query->where('nama_data', 'like', '%' . $request->input('search') . '%');
        }

        // 3. Filter Berdasarkan OPD (Aktifkan kembali dengan benar)
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        // 4. Eksekusi Query menggunakan Paginate agar performa server aman
        $daftardata = $query->get();

        // 5. Ambil data master untuk Dropdown Filter di Blade
        $opds = Opd::all();
        $kegiatans = \App\Models\Kegiatan::all();
        
        return view('data.daftar_data.index', compact('daftardata', 'opds', 'kegiatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opd_id'            => 'required|exists:opd,id',
            'nama_data'         => 'required|string|max:255',
            'satuan'            => 'required|string|max:100',
            'periode'           => 'required|string|max:255',
            'kedalaman_kabkot'  => 'required|string|max:255',
            'sifat_data'        => 'required|string|max:255',
            'sumber_data'       => 'required|string|max:255',
            'kegiatan_id'       => 'nullable|exists:kegiatan,id',
            'aliran_data'        => 'nullable|string|max:5',
            'nama_aliran_data'     => 'nullable|string|max:255',
        ]);

        DaftarData::create($request->all());

        return redirect()->back()->with('success', 'Daftar Data Berhasil Ditambahkan');
    }

    public function edit($id) 
    {
        $daftardata = DaftarData::findOrFail($id);
        $opds = Opd::all();
        return view('data.daftar_data.form', compact('daftardata', 'opds'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'opd_id'            => 'required|exists:opd,id',
            'nama_data'         => 'required|string|max:255',
            'satuan'            => 'required|string|max:100',
            'periode'           => 'required|string|max:255',
            'kedalaman_kabkot'  => 'required|string|max:255',
            'sifat_data'        => 'required|string|max:255',
            'sumber_data'       => 'required|string|max:255',
            'kegiatan_id'       => 'nullable|exists:kegiatan,id',
            'aliran_data'        => 'nullable|string|max:5',
            'nama_aliran_data'     => 'nullable|string|max:255',
        ]);

        $daftardata = DaftarData::findOrFail($id);
        $daftardata->update($request->all());

        return redirect()->back()->with('success', 'Daftar Data Berhasil Diperbarui');
    }

    public function destroy($id)
    {
        $daftardata = DaftarData::findOrFail($id);
        $daftardata->delete();
        return redirect()->back()->with('success', 'Daftar Data Berhasil Dihapus');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new DaftarDataImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function table()
    {
        $query = DaftarData::with(['opd', 'kegiatan']);
        
        // Jika role produsen, hanya tampilkan data miliknya sendiri berdasarkan opd_id
        if (auth()->user()->role == 'produsen') {
            $query->where('opd_id', auth()->user()->opd_id);
        }
        
        $daftardata = $query->get(); 
        
        return view('data.daftar_data.list_daftardata', compact('daftardata'));
    }

    public function show($id)
    {
        $daftardata = DaftarData::with(['opd', 'kegiatan'])->findOrFail($id);
        return view('data.daftar_data.show', compact('daftardata'));
    }
}