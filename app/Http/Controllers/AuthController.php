<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // Tampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba autentikasi dengan credentials yang sudah divalidasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil, regenerate session untuk keamanan
            $request->session()->regenerate();

            // Jika user adalah admin, redirect ke dashboard admin
            if (auth()->user()->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            // Jika bukan admin (mis. student), redirect ke dashboard biasa
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembalikan error dan hanya isi email yang dipertahankan
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Tampilkan form registrasi
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses registrasi pengguna baru
    public function register(Request $request)
    {
        // Validasi input registrasi
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'alamat' => ['nullable', 'string'],
        ]);

        // Buat pengguna baru, password di-hash, set role default 'student'
        $pengguna = Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'alamat' => $request->alamat,
            'role' => 'student',
        ]);

        // Login otomatis pengguna yang baru dibuat
        Auth::login($pengguna);

        // Redirect ke dashboard user
        return redirect('/dashboard');
    }

    // Proses logout
    public function logout(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidate session dan regenerate CSRF token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke homepage
        return redirect('/');
    }
}
