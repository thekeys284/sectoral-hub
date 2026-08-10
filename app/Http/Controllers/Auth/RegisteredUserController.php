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
        $opds = Opd::all(); // Ambil semua data OPD

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
            'nip' => ['nullable', 'string', 'max:255', 'unique:users,nip'],
        ];

        $isSpecialReferral = ($request->referral_code === 'PSSEPSS35');

        if ($isSpecialReferral) {
            $rules['opd_id'] = ['required', 'exists:opd,id'];
        } else {
            // Tanpa kode referral khusus, opd_id opsional (user jadi 'operator', tidak terikat OPD)
            $rules['opd_id'] = ['nullable', 'exists:opd,id'];
        }

        $request->validate($rules);

        $opdId = $isSpecialReferral ? $request->opd_id : null;

        // ------------------------------------------------------------------
        // Penentuan role:
        // - Tanpa kode referral               -> operator
        // - Pakai kode referral + OPD = BPS    -> pembina
        // - Pakai kode referral + OPD = Diskominfo -> walidata
        // - Pakai kode referral + OPD lainnya  -> produsen
        // ------------------------------------------------------------------
        if ($isSpecialReferral) {
            $opd = Opd::find($opdId);
            $opdName = strtolower($opd->name ?? '');

            if (str_contains($opdName, 'badan pusat statistik')) {
                $role = 'pembina';
            } elseif (str_contains($opdName, 'diskominfo')) {
                $role = 'walidata';
            } else {
                $role = 'produsen';
            }
        } else {
            $role = 'operator';
        }

        $user = User::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // JANGAN json_encode manual di sini -> kolom 'role' sudah di-cast 'array' di Model User,
            // Eloquent otomatis json_encode saat disimpan. Kalau di-encode manual, hasilnya
            // ke-encode DUA KALI dan data role di database jadi rusak (sama seperti bug di UserController).
            'role' => [$role],
            'opd_id' => $opdId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}