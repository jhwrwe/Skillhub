<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('pengguna')->get()->map(function ($k) {
            $k->status = $k->calculateStatus();
            return $k;
        });

        $myKelas = auth()->user()->kelas->pluck('id')->toArray();

        return view('kelas.index', compact('kelas', 'myKelas'));
    }

    public function join(Kelas $kelas)
    {
        $user = auth()->user();

        if ($kelas->calculateStatus() === 'completed') {
            return back()->with('error', 'Tidak dapat bergabung ke kelas yang sudah selesai.');
        }

        if ($user->kelas->contains($kelas->id)) {
            return back()->with('error', 'Kamu sudah terdaftar di kelas ini.');
        }

        $user->kelas()->attach($kelas->id, [
            'registration_date' => now(),
        ]);

        return back()->with('success', 'Berhasil bergabung ke kelas ' . $kelas->nama_kelas);
    }

    public function leave(Kelas $kelas)
    {
        auth()->user()->kelas()->detach($kelas->id);

        return back()->with('success', 'Berhasil keluar dari kelas ' . $kelas->nama_kelas);
    }

    public function myKelas()
    {
        $kelas = auth()->user()->kelas->map(function ($k) {
            $k->status = $k->calculateStatus();
            return $k;
        });

        return view('kelas.my-kelas', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruktor' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        $kelas = Kelas::create($validated);

        $kelas->status = $kelas->calculateStatus();
        $kelas->save();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan dengan status: ' . ucfirst($kelas->status));
    }

    public function adminIndex()
    {
        $kelas = Kelas::withCount('pengguna')->get()->map(function ($k) {
            $k->status = $k->calculateStatus();
            return $k;
        });

        return view('admin.kelas.index', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruktor' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        $kelas->update($validated);
        $kelas->status = $kelas->calculateStatus();
        $kelas->save();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diupdate. Status saat ini: ' . ucfirst($kelas->status));
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('pengguna');
        $kelas->status = $kelas->calculateStatus();

        $availablePeserta = Pengguna::whereNotIn('id', $kelas->pengguna->pluck('id'))
            ->where('role', 'student')
            ->get();

        return view('admin.kelas.show', compact('kelas', 'availablePeserta'));
    }

    public function daftarkanPeserta(Request $request, Kelas $kelas)
    {
        $request->validate([
            'pengguna_id' => ['required', 'exists:pengguna,id'],
        ]);

        $pengguna = Pengguna::findOrFail($request->pengguna_id);

        if ($kelas->calculateStatus() === 'completed') {
            return back()->with('error', 'Tidak dapat mendaftarkan peserta ke kelas yang sudah selesai.');
        }

        if ($kelas->pengguna->contains($pengguna->id)) {
            return back()->with('error', 'Peserta sudah terdaftar di kelas ini.');
        }

        $kelas->pengguna()->attach($pengguna->id, [
            'registration_date' => now(),
        ]);

        return back()->with('success', 'Peserta ' . $pengguna->nama_lengkap . ' berhasil ditambahkan ke kelas.');
    }

    public function removePeserta(Kelas $kelas, Pengguna $pengguna)
    {
        $kelas->pengguna()->detach($pengguna->id);

        return back()->with('success', 'Peserta berhasil dikeluarkan dari kelas.');
    }

    public function showDetail(Kelas $kelas)
    {
        $kelas->load('pengguna');
        $kelas->status = $kelas->calculateStatus();
        $isEnrolled = auth()->user()->kelas->contains($kelas->id);

        return view('kelas.detail', compact('kelas', 'isEnrolled'));
    }
}
