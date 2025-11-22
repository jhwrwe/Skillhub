<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount('pengguna')->get();
        $myKelas = auth()->user()->kelas->pluck('id')->toArray();

        return view('kelas.index', compact('kelas', 'myKelas'));
    }

    public function join(Kelas $kelas)
    {
        $user = auth()->user();

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
        $kelas = auth()->user()->kelas;

        return view('kelas.my-kelas', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruktor' => 'required|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function adminIndex()
    {
        $kelas = Kelas::withCount('pengguna')->get();

        return view('admin.kelas.index', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruktor' => 'required|string|max:255',
            'status' => 'required|in:upcoming,ongoing,completed',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        $kelas->update($request->all());

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('pengguna');
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
        $isEnrolled = auth()->user()->kelas->contains($kelas->id);

        return view('kelas.detail', compact('kelas', 'isEnrolled'));
    }

}
