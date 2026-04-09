<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
Use App\Models\Kegiatan;
Use App\Models\Metadata;    
use App\Models\Opd;
use App\Models\Romantik;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KegiatanImport;



class KegiatanController extends Controller
{
    public function index(){
        $kegiatan = Kegiatan::with('opd', 'metadata', 'romantik')->get();
        return view('master.kegiatan.index', compact('kegiatan'));  
    }

    public function create() {
        $kegiatan = new Kegiatan(); 
        $opds = Opd::all();
        $metadatas = Metadata::all();
        $romantiks = Romantik::all();
        return view('master.kegiatan.form', compact('kegiatan', 'opds', 'metadatas', 'romantiks'));
    }

    public function store(Request $request){
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'periode_kegiatan' => 'required|string|max:255',
            'tahun_kegiatan' => 'required|integer',
            'cara_pengumpulan_data' => 'required|string',
            'data_utama' => 'required|string',
            'data_prioritas' => 'required|string',
            'aksesibilitas' => 'required|string',
            'opd_id' => 'required|exists:opd,id',
            'deskripsi' => 'nullable|string',
            'metadata_id' => 'nullable|exists:metadata,id',
            'romantik_id' => 'nullable|exists:romantik,id',
        ]);

        Kegiatan::create($request->all());

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan created successfully.');
    }

    public function edit(Kegiatan $kegiatan) {
        $opds = Opd::all();
        $metadatas = Metadata::all();
        $romantiks = Romantik::all();
        return view('master.kegiatan.form', compact('kegiatan', 'opds', 'metadatas', 'romantiks'));
    }

    public function update(Request $request, Kegiatan $kegiatan){
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'periode_kegiatan' => 'required|string|max:255',
            'tahun_kegiatan' => 'required|integer',
            'cara_pengumpulan_data' => 'required|string',
            'data_utama' => 'required|string',
            'data_prioritas' => 'required|string',
            'aksesibilitas' => 'required|string',
            'opd_id' => 'required|exists:opd,id',
            'deskripsi' => 'nullable|string',
            'metadata_id' => 'nullable|exists:metadata,id',
            'romantik_id' => 'nullable|exists:romantik,id',
        ]);

        $kegiatan->update($request->all());

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan updated successfully.');
    }

    public function destroy(Kegiatan $kegiatan){
        $kegiatan->delete();
        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan deleted successfully.');    
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new KegiatanImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

}
