@extends('layouts.app')

@section('title', 'Admin Dashboard - SkillHub')

@section('content')
<h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Total Siswa</h3>
        <p class="text-3xl font-bold text-blue-500">{{ $totalPengguna }}</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Total Kelas</h3>
        <p class="text-3xl font-bold text-purple-500">{{ $totalKelas }}</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Kelas Berjalan</h3>
        <p class="text-3xl font-bold text-green-500">{{ $kelasOngoing }}</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Kelas Akan Datang</h3>
        <p class="text-3xl font-bold text-yellow-500">{{ $kelasUpcoming }}</p>
    </div>
</div>

<div class="flex gap-4 mb-8">
    <a href="{{ route('admin.kelas.create') }}" class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg font-semibold">
        + Tambah Kelas Baru
    </a>
    <a href="{{ route('admin.kelas.index') }}" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-lg font-semibold">
        Kelola Semua Kelas
    </a>
</div>

<h2 class="text-xl font-semibold mb-4">Kelas Terbaru</h2>
<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-700">
            <tr>
                <th class="text-left px-6 py-3">Nama Kelas</th>
                <th class="text-left px-6 py-3">Instruktor</th>
                <th class="text-left px-6 py-3">Status</th>
                <th class="text-left px-6 py-3">Waktu Mulai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentKelas as $kelas)
                <tr class="border-t border-gray-700">
                    <td class="px-6 py-4">{{ $kelas->nama_kelas }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $kelas->instruktor }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($kelas->status == 'ongoing') bg-green-600
                            @elseif($kelas->status == 'upcoming') bg-yellow-600
                            @else bg-gray-600 @endif">
                            {{ ucfirst($kelas->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $kelas->waktu_mulai->format('d M Y, H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">Belum ada kelas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
