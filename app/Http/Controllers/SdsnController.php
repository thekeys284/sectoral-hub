<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sdsn;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SdsnImport;

class SdsnController extends Controller
{
    public function index(Request $request)
    {
        $sdsn = Sdsn::all();        
        return view('master.sdsn.index', compact('sdsn'));
    }

    public function create()
    {
        $sdsn = new Sdsn();
        return view('master.sdsn.form', compact('sdsn'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_sdsn'             => 'required|string|max:255|unique:sdsn,kode_sdsn',
            'nama_data'             => 'required|string|max:255',
            'konsep'                => 'required|string',
            'definisi'              => 'required|string',
            'klasifikasi_penyajian' => 'nullable|string|max:255',
            'ukuran'                => 'nullable|string|max:255',
            'satuan'                => 'nullable|string|max:255',
            'tahun_penetapan'       => 'required|date',
        ]);

        Sdsn::create($request->all());

        return redirect()->route('master.sdsn.index')->with('success', 'SDSN Berhasil Ditambahkan');
    }

    public function edit($id) 
    {
        $sdsn = Sdsn::findOrFail($id);
        return view('master.sdsn.form', compact('sdsn'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_sdsn'             => 'required|string|max:255|unique:sdsn,kode_sdsn,'.$id,
            'nama_data'             => 'required|string|max:255',
            'konsep'                => 'required|string',
            'definisi'              => 'required|string',
            'klasifikasi_penyajian' => 'nullable|string|max:255',
            'ukuran'                => 'nullable|string|max:255',
            'satuan'                => 'nullable|string|max:255',
            'tahun_penetapan'       => 'required|date',
        ]);

        $sdsn = Sdsn::findOrFail($id);
        $sdsn->update($request->all());

        return redirect()->route('master.sdsn.index')->with('success', 'SDSN Berhasil Diperbarui');
    }

    public function destroy($id)
    {
        $sdsn = Sdsn::findOrFail($id);
        $sdsn->delete();
        return redirect()->route('master.sdsn.index')->with('success', 'SDSN Berhasil Dihapus');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new SdsnImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
