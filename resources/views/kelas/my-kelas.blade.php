@extends('layouts.app')

@section('title', 'Kelas Saya - SkillHub')

@section('content')
<h1 class="text-3xl font-bold mb-6">Kelas Saya</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($kelas as $k)
        <div class="bg-gray-800 rounded-xl p-6">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-xl font-semibold">{{ $k->nama_kelas }}</h3>
                <span class="px-2 py-1 text-xs rounded-full
                    @if($k->status == 'ongoing') bg-green-600
                    @elseif($k->status == 'upcoming') bg-yellow-600
                    @else bg-gray-600 @endif">
                    {{ ucfirst($k->status) }}
                </span>
            </div>

            <p class="text-gray-400 text-sm mb-3">{{ Str::limit($k->deskripsi, 100) }}</p>

            <div class="text-sm text-gray-500 mb-2">
                <p>Instruktor: {{ $k->instruktor }}</p>
                <p>Mulai: {{ $k->waktu_mulai->format('d M Y, H:i') }}</p>
                <p>Selesai: {{ $k->waktu_selesai->format('d M Y, H:i') }}</p>
            </div>

            <p class="text-xs text-blue-400 mb-4">
                Terdaftar: {{ $k->pivot->registration_date ? \Carbon\Carbon::parse($k->pivot->registration_date)->format('d M Y') : '-' }}
            </p>

            <form action="{{ route('kelas.leave', $k) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg transition">
                    Keluar dari Kelas
                </button>
            </form>
        </div>
    @empty
        <div class="col-span-3 bg-gray-800 rounded-xl p-6 text-center">
            <p class="text-gray-400">Kamu belum mengikuti kelas apapun.</p>
            <a href="{{ route('kelas.index') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg">
                Lihat Kelas Tersedia
            </a>
        </div>
    @endforelse
</div>
@endsection
