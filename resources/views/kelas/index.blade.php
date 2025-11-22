@extends('layouts.app')

@section('title', 'Kelas Saya - SkillHub')

@section('content')

@forelse($kelas as $k)
    <a href="{{ route('kelas.detail', $k) }}" class="block">
        <div class="bg-gray-800 rounded-xl p-6 hover:bg-gray-750 transition cursor-pointer h-full">
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

            <div class="text-xs text-blue-400 mb-4">
                <p>👥 {{ $k->pengguna_count ?? 0 }} peserta</p>
            </div>

            @if(in_array($k->id, $myKelas))
                <div class="bg-green-600/20 border border-green-600 text-green-400 py-2 px-3 rounded-lg text-center text-sm font-semibold">
                    ✓ Sudah Terdaftar
                </div>
            @else
                <div class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-center text-sm font-semibold">
                    + Gabung Kelas
                </div>
            @endif
        </div>
    </a>
@empty
    <div class="col-span-3 bg-gray-800 rounded-xl p-6 text-center">
        <p class="text-gray-400">Belum ada kelas tersedia.</p>
    </div>
@endforelse
@endsection
