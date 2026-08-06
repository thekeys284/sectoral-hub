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
            // Ensure opd_id is nullable if no special referral code is used
            // This explicitly allows opd_id to be null if not provided, and validates if it is.
            $rules['opd_id'] = ['nullable', 'exists:opd,id'];
        }

        $request->validate($rules);

        // Determine role and opd_id based on referral code
        $role = $isSpecialReferral ? 'produsen' : 'operator';
        $opdId = $isSpecialReferral ? $request->opd_id : null;

        $user = User::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => json_encode([$role]), // Store role as a JSON array
            'opd_id' => $opdId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // return redirect(route('dashboard', absolute: false));
        return redirect()->route('dashboard'); 

    }
}
