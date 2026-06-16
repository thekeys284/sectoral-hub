<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Menampilkan halaman profil
    public function edit()
    {
        $user = Auth::user();
        $opdBinaan = [];

        $roles = is_string($user->role) ? json_decode($user->role, true) ?? [$user->role] : (array) $user->role;
        $activeRole = session('active_role', $roles[0] ?? '');

        if ($activeRole === 'pembina') {
            $opdBinaan = \App\Models\Opd::where('pembina_id', $user->id)->get();
            // Mengambil data PIC/Produsen dari masing-masing OPD binaan
            foreach ($opdBinaan as $opd) {
                $opd->pic = \App\Models\User::where('opd_id', $opd->id)->where('role', 'produsen')->get();
            }
        }

        return view('pages.profile', compact('user', 'opdBinaan', 'roles', 'activeRole'));
    }

    // Mengupdate informasi profil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($request->all());

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    // Mengupdate Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile')->with('success', 'Password berhasil diganti.');
    }

    // Switch Role Mode (Development/Testing)
    public function switchRole(Request $request)
    {
        $user = Auth::user();
        $roles = is_string($user->role) ? json_decode($user->role, true) ?? [$user->role] : (array) $user->role;

        $request->validate([
            'target_role' => 'required|in:' . implode(',', $roles)
        ]);

        session(['active_role' => $request->target_role]);

        return redirect()->back()->with('success', 'Berhasil berganti role menjadi ' . strtoupper($request->target_role));
    }
}