@extends('layouts.app')

@section('title', 'Dashboard - SkillHub')

@section('content')
<h1 class="text-3xl font-bold mb-6">Selamat Datang, {{ $user->nama_lengkap }}!</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Kelas Diikuti</h3>
        <p class="text-3xl font-bold text-blue-500">{{ $myKelas->count() }}</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Kelas Sedang Berjalan</h3>
        <p class="text-3xl font-bold text-green-500">{{ $ongoingKelas->count() }}</p>
    </div>
    <div class="bg-gray-800 rounded-xl p-6">
        <h3 class="text-gray-400 text-sm">Status</h3>
        <p class="text-xl font-bold text-yellow-500 capitalize">{{ $user->role }}</p>
    </div>
</div>

<h2 class="text-xl font-semibold mb-4">Kelas yang Kamu Ikuti</h2>
@if($myKelas->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($myKelas as $kelas)
            <div class="bg-gray-800 rounded-xl p-6">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg font-semibold">{{ $kelas->nama_kelas }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($kelas->status == 'ongoing') bg-green-600
                        @elseif($kelas->status == 'upcoming') bg-yellow-600
                        @else bg-gray-600 @endif">
                        {{ ucfirst($kelas->status) }}
                    </span>
                </div>
                <p class="text-gray-400 text-sm mb-2">Instruktor: {{ $kelas->instruktor }}</p>
                <p class="text-gray-500 text-xs">{{ $kelas->waktu_mulai->format('d M Y, H:i') }}</p>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-gray-800 rounded-xl p-6 text-center">
        <p class="text-gray-400">Kamu belum mengikuti kelas apapun.</p>
        <a href="{{ route('kelas.index') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg">
            Lihat Kelas Tersedia
        </a>
    </div>
@endif
@endsection
