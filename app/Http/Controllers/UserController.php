<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Opd;
use App\Imports\UserImport;

class UserController extends Controller
{
    public function index(){
        $query = User::with('opd');

        $userRoles = is_string(auth()->user()->role) ? json_decode(auth()->user()->role, true) ?? [auth()->user()->role] : (array) auth()->user()->role;
        $activeRole = session('active_role', $userRoles[0] ?? '');

        // Role Walidata
        if ($activeRole == 'walidata') {
            $query->whereHas('opd', function ($q) {
                $q->where('opd_id', '!=', 1);
            });
        }
        // Role Admin
        $users = $query->get();
        return view('master.user.index', compact('users'));
    }

    public function create() {
        $user = new User();
        $opds = Opd::all();
        return view('master.user.form', compact('user', 'opds'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => 'nullable|string|max:255|unique:users,nip', // NIP opsional dan unik
            'role' => 'required|array|min:1',                    // role sekarang WAJIB array (multi-select)
            'role.*' => 'in:admin,pembina,walidata,produsen',    // tiap item harus salah satu dari daftar ini
            'opd_id' => 'nullable|exists:opd,id',
            'no_hp' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['image', 'referral_code']);

        // JANGAN json_encode manual di sini -> kolom 'role' sudah di-cast 'array' di Model User,
        // Eloquent otomatis json_encode saat disimpan. Kalau di-encode manual di sini, hasilnya
        // ke-encode DUA KALI (double-encoded) dan nilai di database jadi rusak/berkutip aneh.
        $data['role'] = $request->role; // cukup array biasa, misal ['admin','pembina']

        if ($request->hasFile('image')){
            $data['profile_photo_path'] = $request->file('image')->store('profile-photos', 'public');
        }

        User::create($data);

        return redirect()->route('master.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user) {
        $opds = Opd::all();
        return view('master.user.form', compact('user', 'opds'));
    }

    public function update(Request $request, User $user){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nip' => 'nullable|string|max:255|unique:users,nip,' . $user->id,
            'role' => 'required|array|min:1',
            'role.*' => 'in:admin,pembina,walidata,produsen',
            'opd_id' => 'nullable|exists:opd,id',
            'no_hp' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['password', 'image', 'referral_code']);

        // Sama seperti store(): jangan json_encode manual, biarkan Model yang menangani via cast.
        $data['role'] = $request->role;

        if ($request->hasFile('image')) {
            if ($user->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('image')->store('profile-photos', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('master.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user){
        if ($user->profile_photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->delete();
        return redirect()->route('master.users.index')->with('success', 'User deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new UserImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}