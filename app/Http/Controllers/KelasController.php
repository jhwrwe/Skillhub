<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Carbon\Carbon;


class KelasController extends Controller
{
    // Tampilkan daftar semua kelas untuk user biasa
    public function index()
    {
        // Ambil semua kelas berserta jumlah peserta (withCount)
        // Lalu map untuk menambahkan properti status (menggunakan calculateStatus di model)
        $kelas = Kelas::withCount('pengguna')->get()->map(function ($k) {
            $k->status = $k->calculateStatus(); // status: upcoming/ongoing/completed
            return $k;
        });

        // Ambil daftar id kelas yang diikuti user saat ini, untuk menandai tombol join/leave
        $myKelas = auth()->user()->kelas->pluck('id')->toArray();

        return view('kelas.index', compact('kelas', 'myKelas'));
    }

    // User bergabung ke kelas tertentu
    public function join(Kelas $kelas)
    {
        $user = auth()->user();

        // Cek status kelas, jika sudah selesai maka tidak boleh bergabung
        if ($kelas->calculateStatus() === 'completed') {
            return back()->with('error', 'Tidak dapat bergabung ke kelas yang sudah selesai.');
        }

        // Cek apakah user sudah terdaftar, jika sudah maka batalkan
        if ($user->kelas->contains($kelas->id)) {
            return back()->with('error', 'Kamu sudah terdaftar di kelas ini.');
        }

        // Attach relasi many-to-many di pivot table dan simpan tanggal pendaftaran
        $user->kelas()->attach($kelas->id, [
            'registration_date' => now(),
        ]);

        return back()->with('success', 'Berhasil bergabung ke kelas ' . $kelas->nama_kelas);
    }

    // User keluar dari kelas
    public function leave(Kelas $kelas)
    {
        // Hapus relasi pivot antara user dan kelas
        auth()->user()->kelas()->detach($kelas->id);

        return back()->with('success', 'Berhasil keluar dari kelas ' . $kelas->nama_kelas);
    }

    // Tampilkan kelas yang diikuti user saat ini
    public function myKelas()
    {
        // Ambil kelas user lalu tambahkan properti status untuk masing-masing kelas
        $kelas = auth()->user()->kelas->map(function ($k) {
            $k->status = $k->calculateStatus();
            return $k;
        });

        return view('kelas.my-kelas', compact('kelas'));
    }

    // Tampilkan form pembuatan kelas (admin)
    public function create()
    {
        return view('admin.kelas.create');
    }

    // Simpan kelas baru yang dibuat admin
    public function store(Request $request)
    {
        // Validasi input, pastikan waktu selesai setelah waktu mulai
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruktor' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        // Buat record kelas
        $kelas = Kelas::create($validated);

        // Hitung status berdasarkan waktu dan simpan
        $kelas->status = $kelas->calculateStatus();
        $kelas->save();

        // Redirect ke index admin kelas dengan pesan sukses dan status awal
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan dengan status: ' . ucfirst($kelas->status));
    }

    // Tampilkan daftar kelas untuk admin (dengan jumlah peserta)
    public function adminIndex()
    {
        $kelas = Kelas::withCount('pengguna')->get()->map(function ($k) {
            $k->status = $k->calculateStatus();
            return $k;
        });

        return view('admin.kelas.index', compact('kelas'));
    }

    // Tampilkan form edit kelas (admin)
    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    // Update data kelas oleh admin
    public function update(Request $request, Kelas $kelas)
    {
        // Validasi input mirip dengan store
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruktor' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        // Update record kelas
        $kelas->update($validated);

        // Recalculate status dan simpan
        $kelas->status = $kelas->calculateStatus();
        $kelas->save();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diupdate. Status saat ini: ' . ucfirst($kelas->status));
    }

    // Hapus kelas oleh admin
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    // Tampilkan detail kelas untuk admin (lihat peserta dan peserta yang tersedia)
    public function show(Kelas $kelas)
    {
        // Load relasi pengguna untuk ditampilkan
        $kelas->load('pengguna');

        // Hitung status kelas
        $kelas->status = $kelas->calculateStatus();

        // Ambil daftar pengguna (student) yang belum terdaftar di kelas ini
        $availablePeserta = Pengguna::whereNotIn('id', $kelas->pengguna->pluck('id'))
            ->where('role', 'student')
            ->get();

        return view('admin.kelas.show', compact('kelas', 'availablePeserta'));
    }

    // Admin mendaftarkan peserta ke kelas tertentu
    public function daftarkanPeserta(Request $request, Kelas $kelas)
    {
        // Validasi input pengguna yang akan didaftarkan
        $request->validate([
            'pengguna_id' => ['required', 'exists:pengguna,id'],
        ]);

        $pengguna = Pengguna::findOrFail($request->pengguna_id);

        // Cek apakah kelas sudah selesai
        if ($kelas->calculateStatus() === 'completed') {
            return back()->with('error', 'Tidak dapat mendaftarkan peserta ke kelas yang sudah selesai.');
        }

        // Cek apakah peserta sudah terdaftar
        if ($kelas->pengguna->contains($pengguna->id)) {
            return back()->with('error', 'Peserta sudah terdaftar di kelas ini.');
        }

        // Attach peserta ke kelas dengan tanggal pendaftaran
        $kelas->pengguna()->attach($pengguna->id, [
            'registration_date' => now(),
        ]);

        return back()->with('success', 'Peserta ' . $pengguna->nama_lengkap . ' berhasil ditambahkan ke kelas.');
    }

    // Admin mengeluarkan peserta dari kelas
    public function removePeserta(Kelas $kelas, Pengguna $pengguna)
    {
        // Detach relasi pivot antara kelas dan pengguna
        $kelas->pengguna()->detach($pengguna->id);

        return back()->with('success', 'Peserta berhasil dikeluarkan dari kelas.');
    }

    // Tampilkan detail kelas untuk user biasa (lihat apakah user terdaftar)
    public function showDetail(Kelas $kelas)
    {
        // Load relasi pengguna untuk tampilan
        $kelas->load('pengguna');

        // Hitung status kelas
        $kelas->status = $kelas->calculateStatus();

        // Cek apakah user yang sedang login sudah terdaftar di kelas ini
        $isEnrolled = auth()->user()->kelas->contains($kelas->id);

        return view('kelas.detail', compact('kelas', 'isEnrolled'));
    }
}
