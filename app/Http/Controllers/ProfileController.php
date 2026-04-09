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
        return view('pages.profile', [
            'user' => Auth::user()
        ]);
    }

    // Mengupdate informasi profil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Tambahkan kolom lain jika perlu, misal: 'nip' => ['nullable', 'string']
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
}