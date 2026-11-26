<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Opd; // Import model Opd
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $opds = Opd::select('id', 'name')->orderBy('name', 'asc')->get();

        return view('auth.register', compact('opds'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'max:255'],
        ];

        $isSpecialReferral = ($request->referral_code === 'PSSEPSS35');

        if ($isSpecialReferral) {
            $rules['opd_id'] = ['required', 'exists:opd,id'];
            $rules['nip'] = ['required', 'string', 'max:255', 'unique:users,nip'];
        } else {
            // Tanpa kode referral khusus, opd_id & nip opsional
            $rules['opd_id'] = ['nullable', 'exists:opd,id'];
            $rules['nip'] = ['nullable', 'string', 'max:255', 'unique:users,nip'];
        }

        $request->validate($rules);

        $opdId = $isSpecialReferral ? $request->opd_id : null;
        $nip = $isSpecialReferral ? $request->nip : null;

        // ------------------------------------------------------------------
        // Penentuan role saat Register (Opsi A):
        // - Baik tanpa kode referral maupun dengan kode PSSEPSS35,
        //   pendaftar baru masuk dengan role awal 'operator' (Akun Pribadi / Pelatihan,
        //   tanpa akses pengelolaan data sektoral).
        // - Untuk mendapatkan role 'produsen' / 'pembina' / 'walidata', user dapat
        //   memasukkan kode referal DATASEKTORAL35 di halaman Profil (atau diatur Admin).
        // ------------------------------------------------------------------
        $role = 'operator';

        $user = User::create([
            'name' => $request->name,
            'nip' => $nip,
            'email' => $request->email,
            'password' => $request->password, // Otomatis di-hash sekali saja melalui cast 'password' => 'hashed' di Model User
            'role' => [$role],
            'opd_id' => $opdId,
        ]);

        event(new Registered($user));

        Auth::login($user);
        session(['active_role' => $role]);

        return redirect()->route('dashboard');
    }
}