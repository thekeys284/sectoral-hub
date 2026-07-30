<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Handle Majapahit Token Login Callback
     */
    public function loginMajapahit(Request $request): RedirectResponse
    {
        $token = $request->get('token') ?? $request->header('Authorization');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Token Majapahit tidak ditemukan.');
        }

        try {
            // Bersihkan prefix 'Bearer ' jika ada
            if (str_contains($token, 'Bearer ')) {
                $token = explode(' ', $token)[1];
            }

            // Ambil Secret Key Majapahit dari config / .env
            $majapahitKey = config('services.majapahit.key', env('MAJAPAHIT_KEY'));

            // Decode JWT Token
            $decoded = JWT::decode($token, new Key($majapahitKey, 'HS256'));
            $email = $decoded->email ?? null;

            if (!$email) {
                return redirect()->route('login')->with('error', 'Email tidak ditemukan pada token.');
            }

            // Cari user berdasarkan email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Autentikasi user di Laravel
                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard', absolute: false))
                    ->with('success', 'Login berhasil melalui Majapahit!');
            }

            return redirect()->route('login')->with('error', 'Akun dengan email ' . $email . ' belum terdaftar pada aplikasi.');

        } catch (\Firebase\JWT\ExpiredException $e) {
            return redirect()->route('login')->with('error', 'Token Majapahit telah kedaluwarsa.');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal memproses token: ' . $e->getMessage());
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}