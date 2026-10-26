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
        $query = Kegiatan::with(['opd', 'metadata', 'romantik'])->latest();
        $user = auth()->user();

        $roles = is_string($user->role) ? json_decode($user->role, true) ?? [$user->role] : (array) $user->role;
        $activeRole = session('active_role', $roles[0] ?? '');
        
        if ($activeRole === 'produsen') {
            $query->where('opd_id', $user->opd_id);
        }
        elseif ($activeRole === 'pembina') {
            $opdBinaanIds = \App\Models\Opd::where('pembina_id', $user->id)->pluck('id')->toArray();
            
            $query->whereIn('opd_id', $opdBinaanIds);
        }

        $kegiatan = $query->get();
        
        return view('master.kegiatan.index', compact('kegiatan'));
    } 

    public function create() {
        $kegiatan = new Kegiatan();
        if (auth()->check() && auth()->user()->opd_id) {
            $kegiatan->opd_id = auth()->user()->opd_id;
        }
        $opds = Opd::whereNull('parent_id')->orderBy('name', 'asc')->get();
        $metadatas = Metadata::all();
        $romantiks = Romantik::all();
        return view('master.kegiatan.form', compact('kegiatan', 'opds', 'metadatas', 'romantiks'));
    }

    public function store(Request $request){
        if (!$request->filled('opd_id') && auth()->check() && auth()->user()->opd_id) {
            $request->merge(['opd_id' => auth()->user()->opd_id]);
        }

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'periode_kegiatan' => 'nullable|string|max:255',
            'tahun_kegiatan' => 'required|integer',
            'cara_pengumpulan_data' => 'nullable|string',
            'data_utama' => 'nullable|string',
            'data_prioritas' => 'nullable|string',
            'aksesbilitas' => 'nullable|string',
            'opd_id' => 'required|exists:opd,id',
            'deskripsi' => 'nullable|string',
            'metadata_id' => 'nullable|exists:metadata,id',
            'romantik_id' => 'nullable|exists:romantik,id',
            'link_metadata_kegiatan' => 'nullable|string',
            'link_metadata_variabel' => 'nullable|string',
            'link_metadata_indikator' => 'nullable|string',
        ]);

        Kegiatan::create($request->all());

        return redirect()->route('master.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan) {
        $opds = Opd::whereNull('parent_id')->orderBy('name', 'asc')->get();
        $metadatas = Metadata::all();
        $romantiks = Romantik::all();
        return view('master.kegiatan.form', compact('kegiatan', 'opds', 'metadatas', 'romantiks'));
    }

    public function update(Request $request, Kegiatan $kegiatan){
        if (!$request->filled('opd_id') && auth()->check() && auth()->user()->opd_id) {
            $request->merge(['opd_id' => auth()->user()->opd_id]);
        }

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'periode_kegiatan' => 'nullable|string|max:255',
            'tahun_kegiatan' => 'required|integer',
            'cara_pengumpulan_data' => 'nullable|string',
            'data_utama' => 'nullable|string',
            'data_prioritas' => 'nullable|string',
            'aksesbilitas' => 'nullable|string',
            'opd_id' => 'required|exists:opd,id',
            'deskripsi' => 'nullable|string',
            'metadata_id' => 'nullable|exists:metadata,id',
            'romantik_id' => 'nullable|exists:romantik,id',
            'link_metadata_kegiatan' => 'nullable|string',
            'link_metadata_variabel' => 'nullable|string',
            'link_metadata_indikator' => 'nullable|string',
        ]);

        $kegiatan->update($request->all());

        return redirect()->route('master.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan){
        $kegiatan->delete();
        return redirect()->route('master.kegiatan.index')->with('success', 'Kegiatan deleted successfully.');    
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
