<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk menampilkan dashboard baik pengguna biasa maupun admin.
 */
class DashboardController extends Controller
{
    // Dashboard untuk user yang login (student)
    public function index()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();

        // Ambil relasi kelas yang diikuti oleh user
        $myKelas = $user->kelas;

        // Ambil kelas yang sedang berlangsung (scope ongoing di model Kelas)
        $ongoingKelas = Kelas::ongoing()->get();

        // Tampilkan view dashboard dengan data yang diperlukan
        return view('dashboard', compact('user', 'myKelas', 'ongoingKelas'));
    }

    // Dashboard untuk admin dengan ringkasan statistik
    public function adminDashboard()
    {
        // Hitung total pengguna dengan role student
        $totalPengguna = Pengguna::where('role', 'student')->count();

        // Hitung total kelas
        $totalKelas = Kelas::count();

        // Hitung jumlah kelas ongoing dan upcoming menggunakan scope di model
        $kelasOngoing = Kelas::ongoing()->count();
        $kelasUpcoming = Kelas::upcoming()->count();

        // Ambil 5 kelas terbaru untuk ringkasan
        $recentKelas = Kelas::latest()->take(5)->get();

        // Kirim data ke view admin.dashboard
        return view('admin.dashboard', compact(
            'totalPengguna',
            'totalKelas',
            'kelasOngoing',
            'kelasUpcoming',
            'recentKelas'
        ));
    }

    // Tampilkan daftar pengguna (student) untuk admin
    public function users()
    {
        // Ambil semua pengguna bertipe student, sertakan juga count relasi kelas
        $users = Pengguna::where('role', 'student')->withCount('kelas')->get();

        // Kirim data ke view admin.users.index
        return view('admin.users.index', compact('users'));
    }
}
