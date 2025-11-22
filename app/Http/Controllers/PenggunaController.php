<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $peserta = Pengguna::withCount('kelas')->get();

        return view('admin.peserta.index', compact('peserta'));
    }

    public function create()
    {
        return view('admin.peserta.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'alamat' => ['nullable', 'string'],
            'role' => ['required', 'in:student,admin'],
        ]);

        Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'alamat' => $request->alamat,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function show(Pengguna $pengguna)
    {
        $pengguna->load('kelas');
        $availableKelas = Kelas::whereNotIn('id', $pengguna->kelas->pluck('id'))->get();

        return view('admin.peserta.show', compact('pengguna', 'availableKelas'));
    }

    public function edit(Pengguna $pengguna)
    {
        return view('admin.peserta.edit', compact('pengguna'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna,email,' . $pengguna->id],
            'alamat' => ['nullable', 'string'],
            'role' => ['required', 'in:student,admin'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil diupdate.');
    }

    public function destroy(Pengguna $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $pengguna->delete();

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil dihapus.');
    }

    public function daftarKelas(Request $request, Pengguna $pengguna)
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        if ($pengguna->kelas->contains($request->kelas_id)) {
            return back()->with('error', 'Peserta sudah terdaftar di kelas ini.');
        }

        $pengguna->kelas()->attach($request->kelas_id, [
            'registration_date' => now(),
        ]);

        return back()->with('success', 'Peserta berhasil didaftarkan ke kelas.');
    }

    public function batalKelas(Pengguna $pengguna, Kelas $kelas)
    {
        $pengguna->kelas()->detach($kelas->id);

        return back()->with('success', 'Pendaftaran peserta berhasil dibatalkan.');
    }
}
