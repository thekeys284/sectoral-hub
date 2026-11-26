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
        $user = Auth::user()->load('opd'); // Eager load relasi OPD
        $opds = \App\Models\Opd::with('parent')->orderBy('name', 'asc')->get();
        $opdBinaan = [];

        $roles = is_string($user->role) ? json_decode($user->role, true) ?? [$user->role] : (array) $user->role;
        $activeRole = session('active_role', $roles[0] ?? '');

        if ($activeRole === 'pembina') {
            $opdBinaan = \App\Models\Opd::where('pembina_id', $user->id)->whereNull('parent_id')->get();
            foreach ($opdBinaan as $opd) {
                $childOpdIds = \App\Models\Opd::where('parent_id', $opd->id)->pluck('id')->push($opd->id);
                $opd->pic = \App\Models\User::whereIn('opd_id', $childOpdIds)
                    ->where(function ($q) {
                        $q->whereJsonContains('role', 'produsen')
                          ->orWhere('role', 'like', '%produsen%');
                    })
                    ->get();
            }
        }

        return view('pages.profile', compact('user', 'opds', 'opdBinaan', 'roles', 'activeRole'));
    }

    // Mengupdate informasi profil
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'opd_id'   => ['nullable', 'exists:opd,id'],
            'nip'      => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $dataToUpdate = $validatedData;
        if (empty($dataToUpdate['opd_id'])) {
            $dataToUpdate['opd_id'] = null;
        }

        // Handle file upload untuk foto profil
        if ($request->hasFile('image')) {
            // Hapus foto profil lama jika ada
            if ($user->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
            }
            // Simpan foto profil baru
            $dataToUpdate['profile_photo_path'] = $request->file('image')->store('profile-photos', 'public');
        } else {
            // Pastikan 'image' tidak masuk ke update jika tidak ada file baru
            unset($dataToUpdate['image']);
        }

        $user->update($dataToUpdate); // Update user dengan data yang sudah divalidasi dan path foto profil

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    // Klaim kode referal DATASEKTORAL35 untuk menambahkan role Produsen / Pembina / Walidata sesuai OPD
    public function claimReferral(Request $request)
    {
        $request->validate([
            'referral_code' => ['required', 'string', 'max:100'],
        ]);

        $code = strtoupper(trim($request->referral_code));

        if ($code !== 'DATASEKTORAL35') {
            return redirect()->route('profile')->with('error', 'Kode referal tidak valid. Pastikan kode yang Anda masukkan benar.');
        }

        $user = Auth::user();
        $rawOpdId = $user->getRawOriginal('opd_id');
        $originOpd = $rawOpdId ? \App\Models\Opd::find($rawOpdId) : null;

        if (!$originOpd) {
            return redirect()->route('profile')->with('error', 'Silakan pilih dan simpan Dinas / Satker Asal Anda pada Informasi Profil terlebih dahulu sebelum memasukkan kode referal.');
        }

        $opdName = strtolower(trim($originOpd->name ?? ''));
        if ($opdName === 'bps' || str_contains($opdName, 'badan pusat statistik')) {
            $targetRole = 'pembina';
        } elseif (str_contains($opdName, 'diskominfo') || str_contains($opdName, 'kominfo')) {
            $targetRole = 'walidata';
        } else {
            $targetRole = 'produsen';
        }

        $currentRoles = is_string($user->role)
            ? (json_decode($user->role, true) ?? [$user->role])
            : (is_array($user->role) ? $user->role : []);

        if (!in_array('operator', $currentRoles)) {
            $currentRoles[] = 'operator';
        }

        if (in_array($targetRole, $currentRoles)) {
            session(['active_role' => $targetRole]);
            return redirect()->route('profile')->with('success', 'Role ' . strtoupper($targetRole) . ' sudah aktif pada akun Anda.');
        }

        $currentRoles[] = $targetRole;

        $user->update([
            'role' => array_values(array_unique($currentRoles)),
        ]);

        session(['active_role' => $targetRole]);

        return redirect()->route('profile')->with('success', 'Kode referal berhasil diverifikasi! Role ' . strtoupper($targetRole) . ' telah ditambahkan dan diaktifkan.');
    }
    // Mengupdate Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.current_password' => 'Password saat ini (lama) yang Anda masukkan salah.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password baru minimal 8 karakter.',
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

        if ($request->target_role === 'operator') {
            return redirect()->route('dashboard')->with('success', 'Berhasil beralih ke Mode Akun Pribadi (Operator).');
        }

        return redirect()->back()->with('success', 'Berhasil berganti mode akun menjadi ' . strtoupper($request->target_role));
    }
}