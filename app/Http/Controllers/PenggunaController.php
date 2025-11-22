<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk manajemen peserta oleh admin:
 * - CRUD peserta, melihat detail peserta, mendaftarkan peserta ke kelas, batalkan pendaftaran
 */
class PenggunaController extends Controller
{
    // Tampilkan semua peserta beserta jumlah kelas yang diikuti
    public function index()
    {
        // withCount('kelas') agar di view kita dapat menampilkan jumlah kelas tiap peserta
        $peserta = Pengguna::withCount('kelas')->get();

        return view('admin.peserta.index', compact('peserta'));
    }

    // Tampilkan form tambah peserta baru (admin)
    public function create()
    {
        return view('admin.peserta.create');
    }

    // Simpan peserta baru (admin)
    public function store(Request $request)
    {
        // Validasi input, termasuk role yang hanya boleh 'student' atau 'admin'
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'alamat' => ['nullable', 'string'],
            'role' => ['required', 'in:student,admin'],
        ]);

        // Buat pengguna baru, password di-hash
        Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'alamat' => $request->alamat,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil ditambahkan.');
    }

    // Tampilkan detail peserta (admin), termasuk kelas yang diikuti dan kelas yang belum diikuti
    public function show(Pengguna $pengguna)
    {
        // Load relasi kelas untuk peserta
        $pengguna->load('kelas');

        // Ambil kelas yang belum diikuti peserta agar admin bisa menambahkan peserta ke kelas tersebut
        $availableKelas = Kelas::whereNotIn('id', $pengguna->kelas->pluck('id'))->get();

        return view('admin.peserta.show', compact('pengguna', 'availableKelas'));
    }

    // Tampilkan form edit peserta (admin)
    public function edit(Pengguna $pengguna)
    {
        return view('admin.peserta.edit', compact('pengguna'));
    }

    // Update data peserta (admin)
    public function update(Request $request, Pengguna $pengguna)
    {
        // Validasi input, cek unique email kecuali untuk email milik peserta yang sedang diupdate
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna,email,' . $pengguna->id],
            'alamat' => ['nullable', 'string'],
            'role' => ['required', 'in:student,admin'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Siapkan data untuk update
        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'role' => $request->role,
        ];

        // Jika password diisi, hash dan sertakan ke data update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update peserta
        $pengguna->update($data);

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil diupdate.');
    }

    // Hapus peserta (admin)
    public function destroy(Pengguna $pengguna)
    {
        // Cegah admin menghapus akun sendiri
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $pengguna->delete();

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil dihapus.');
    }

    // Admin mendaftarkan peserta ke sebuah kelas
    public function daftarKelas(Request $request, Pengguna $pengguna)
    {
        // Validasi input kelas
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);

        // Cek apakah peserta sudah terdaftar pada kelas tersebut
        if ($pengguna->kelas->contains($request->kelas_id)) {
            return back()->with('error', 'Peserta sudah terdaftar di kelas ini.');
        }

        // Attach relasi pivot dengan tanggal pendaftaran
        $pengguna->kelas()->attach($request->kelas_id, [
            'registration_date' => now(),
        ]);

        return back()->with('success', 'Peserta berhasil didaftarkan ke kelas.');
    }

    // Admin membatalkan pendaftaran peserta pada sebuah kelas
    public function batalKelas(Pengguna $pengguna, Kelas $kelas)
    {
        // Hapus relasi pivot antara peserta dan kelas
        $pengguna->kelas()->detach($kelas->id);

        return back()->with('success', 'Pendaftaran peserta berhasil dibatalkan.');
    }
}
