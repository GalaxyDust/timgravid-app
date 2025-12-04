<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses data login yang dikirim user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validasi input: email dan password wajib diisi.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Mencoba proses otentikasi.
        if (Auth::attempt($credentials)) {
            // Jika sukses, buat ulang session (untuk keamanan session fixation).
            $request->session()->regenerate();

            // 3. Arahkan ke URL dashboard yang benar ('/app/dashboard').
            return redirect()->intended('/app/dashboard');
        }

        // 4. Jika gagal, kembalikan ke halaman login dengan pesan error.
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Hanya mengembalikan input email, bukan password.
    }

    /**
     * Proses keluar (Logout).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Hancurkan session saat ini.
        $request->session()->invalidate();

        // Buat token CSRF baru.
        $request->session()->regenerateToken();

        // Arahkan kembali ke halaman login.
        return redirect('/login');
    }
}