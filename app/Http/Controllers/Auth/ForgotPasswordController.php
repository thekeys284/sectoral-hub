<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Tampilkan form minta link
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Kirim email link reset
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar pada sistem kami.'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan reset kata sandi telah dikirim ke email Anda!');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}