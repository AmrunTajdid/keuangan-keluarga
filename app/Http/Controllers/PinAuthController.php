<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PinAuthController extends Controller
{
    public function showLogin()
    {
        if (session('pin_authenticated', false)) {
            return redirect()->route('dashboard');
        }

        return view('auth.pin-login');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ], [
            'pin.required' => 'Silakan masukkan PIN.',
        ]);

        $correctPin = env('FAMILY_PIN', '030923');

        if ($request->pin === (string) $correctPin) {
            $request->session()->regenerate();
            session(['pin_authenticated' => true]);

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang! Brankas keluarga berhasil dibuka.');
        }

        return back()->with('error', 'PIN salah! Silakan masukkan 6 digit PIN yang benar.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('pin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pin.login')->with('success', 'Brankas keuangan keluarga telah dikunci.');
    }
}
