<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Storage;
use Illuminate\Validation\Rules;
use App\Models\Opd;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OpdImport;



class OpdController extends Controller
{
    public function index(){
        $opds = Opd::with('pembina')->get();
        $users = User::all();
        return view('master.opd.index', compact('opds', 'users'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string',
            'email' => 'required|string|lowercase|email|max:255|unique:opd',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' 
        ]);

        $data = $request->all();

        if ($request->hasFile('image')){
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('logos'), $fileName);
            $data['logo_path'] = 'logos/' . $fileName;
        }

        Opd::create($data);

        return redirect()->back()->with('success', 'OPD Berhasil Ditambahkan');
    }

    public function edit(Opd $opd) {
        $opds = Opd::all();
        return view('master.opd.form', compact('opd', 'opds'));
    }

    public function update(Request $request, Opd $opd){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:opd,email,' . $opd->id,
            'alamat' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Hapus logo lama jika ada di folder public
            if ($opd->logo_path && file_exists(public_path($opd->logo_path))) {
                unlink(public_path($opd->logo_path));
            }

            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('logos'), $fileName);
            $data['logo_path'] = 'logos/' . $fileName;
        }

        $opd->update($data);

        return redirect()->back()->with('success', 'OPD Berhasil Diperbarui');

    }

    public function destroy(Opd $opd){
        if ($opd->logo_path && file_exists(public_path($opd->logo_path))) {
            unlink(public_path($opd->logo_path));
        }
        $opd->delete();
        return redirect()->back()->with('success', 'OPD Berhasil Dihapus');
    }   

    public function import(Request $request) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new OpdImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
