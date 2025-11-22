<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $myKelas = $user->kelas;
        $ongoingKelas = Kelas::ongoing()->get();

        return view('dashboard', compact('user', 'myKelas', 'ongoingKelas'));
    }

    public function adminDashboard()
    {
        $totalPengguna = Pengguna::where('role', 'student')->count();
        $totalKelas = Kelas::count();
        $kelasOngoing = Kelas::ongoing()->count();
        $kelasUpcoming = Kelas::upcoming()->count();
        $recentKelas = Kelas::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalPengguna',
            'totalKelas',
            'kelasOngoing',
            'kelasUpcoming',
            'recentKelas'
        ));
    }

    public function users()
    {
        $users = Pengguna::where('role', 'student')->withCount('kelas')->get();

        return view('admin.users.index', compact('users'));
    }
}
