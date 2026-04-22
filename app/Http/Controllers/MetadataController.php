<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\Metadata;
use App\Models\Opd;
use Maatwebsite\Excel\Facades\Excel;    
use App\Imports\MetadataImport;


class MetadataController extends Controller
{
    public function index(){
        $metadata = Metadata::with('opd')->get();
        return view('data.metadata.index', compact('metadata'));
    }

    public function create(){
        $metadata = new Metadata();
        $opds = Opd::all();
        return view('data.metadata.form', compact('opds', 'metadata'));
    }

    public function store (Request $request){
        $request->validate([
            'opd_id' => 'required|exists:opd,id',
            'judul_kegiatan' => 'required|string|max:255',
            'periode_submission' => 'required|integer',
            'tanggal_submission' => 'required|string',
            'status' => 'required|string|max:255'
        ]);

        Metadata::create($request->all());

        return redirect()->route('data.metadata.index')->with('success', 'Metadata created successfully');
    }
    public function show(Metadata $metadata)
        {
            $opds = \App\Models\Opd::all();
            
            return view('data.metadata.show', compact('metadata', 'opds'));
        }
    public function edit(Metadata $metadata){
        $opds = Opd::all();
        $metadatas = Metadata::all();
        return view('data.metadata.form', compact('opds', 'metadata'));
    }

    public function update(Request $request, Metadata $metadata){
        $request->validate([
            'opd_id' => 'required|exists:opd,id',
            'judul_kegiatan' => 'required|string|max:255',
            'periode_submission' => 'required|integer',
            'tanggal_submission' => 'required|string',
            'status' => 'required|string|max:255'
        ]);

        $metadata->update($request->all());

        return redirect()->route('data.metadata.index')->with('success', 'Metadata updated successfully');
    }

    public function destroy(Metadata $metadata){
        $metadata->delete();
        return redirect()->route('data.metadata.index')->with('success', 'Metadata deleted successfully');
    }

   public function table()
    {
        $metadata = Metadata::latest()->get(); 
        
        return view('data.metadata.list_metadata', [
            'metadata' => $metadata 
        ]);
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new MetadataImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
