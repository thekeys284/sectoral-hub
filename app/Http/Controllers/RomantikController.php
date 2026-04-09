<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Romantik;
use App\Models\Opd;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RomantikImport;


class RomantikController extends Controller
{
    public function index(){
        $romantiks = Romantik::with('opd')->get();
        return view('data.romantik.index', compact('romantiks'));
    }

    public function create(){
        $romantik = new Romantik();
        $opds = Opd::all();
        return view('data.romantik.form', compact('opds', 'romantik'));
    }

    public function store (Request $request){
        $request->validate([
            'opd_id' => 'required|exists:opd,id',
            'judul_kegiatan' => 'required|string|max:255',
            'tahun_kegiatan' => 'required|integer',
            'nomor_rekomendasi' => 'required|string|max:255',
            'tgl_pengajuan' => 'required|date',
            'tgl_perbaikan_terakhir' => 'required|date',
            'tgl_selesai' => 'required|date',
            'lama_pemeriksaan' => 'required|string',
            'status_pengajuan' => 'required|string|max:255',
            'status_rekomendasi' => 'required|string|max:255',
        ]);

        Romantik::create($request->all());

        return redirect()->route('admin.romantik.index')->with('success', 'Romantik created successfully');
    }

    public function edit(Romantik $romantik){
        $opds = Opd::all();
        return view('data.romantik.form', compact('opds', 'romantik'));
    }

    public function show(Romantik $romantik)
    {
        $romantik->load('opd');
        
        return view('data.romantik.form', [
            'romantik' => $romantik,
            'opds'     => Opd::all(),
            'is_show'  => true
        ]);
    }

    public function update(Request $request, Romantik $romantik){
         $request->validate([
            'opd_id' => 'required|exists:opd,id',
            'judul_kegiatan' => 'required|string|max:255',
            'tahun_kegiatan' => 'required|integer',
            'nomor_rekomendasi' => 'required|string|max:255',
            'tgl_pengajuan' => 'required|date',
            'tgl_perbaikan_terakhir' => 'required|date',
            'tgl_selesai' => 'required|date',
            'lama_pemeriksaan' => 'required|string',
            'status_pengajuan' => 'required|string|max:255',
            'status_rekomendasi' => 'required|string|max:255',
        ]); 

        $romantik->update($request->all());            
        return redirect()->route('admin.romantik.index')->with('success', 'Romantik updated successfully');
    }

    public function destroy(Romantik $romantik){
        $romantik->delete();
        return redirect()->route('admin.romantik.index')->with('success', 'Romantik deleted successfully');
    }

    public function table()
    {
        $romantik = Romantik::latest()->get(); 
        
        return view('data.romantik.list_romantik', [
            'romantik' => $romantik 
        ]);
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new RomantikImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
