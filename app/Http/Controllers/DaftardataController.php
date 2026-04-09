<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opd;
use App\Models\DaftarData;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DaftarDataImport;


class DaftardataController extends Controller
{
    public function index()
    {
        $daftardata = DaftarData::with('opd')->get();
        $opds = Opd::all();
        
        return view('data.daftar_data.index', compact('daftardata', 'opds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opd_id'            => 'required|exists:opd,id',
            'nama_data'         => 'required|string|max:255',
            'satuan'            => 'required|string|max:100',
            'periode'           => 'required|string|max:255',
            'kedalaman_kabkot'  => 'required|boolean',
            'sifat_data'        => 'required|string|max:255',
            'sumber_data'       => 'required|string|max:255',
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
            'kedalaman_kabkot'  => 'required|boolean',
            'sifat_data'        => 'required|string|max:255',
            'sumber_data'       => 'required|string|max:255',
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
}