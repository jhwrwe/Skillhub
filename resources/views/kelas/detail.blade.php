@extends('layouts.app')

@section('title', $kelas->nama_kelas . ' - SkillHub')

@section('content')
<div class="mb-6">
    <a href="{{ route('kelas.index') }}" class="text-blue-500 hover:underline">← Kembali ke Semua Kelas</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Info Kelas -->
    <div class="lg:col-span-1">
        <div class="bg-gray-800 rounded-xl p-6">
            <h1 class="text-2xl font-bold mb-4">{{ $kelas->nama_kelas }}</h1>

            <span class="inline-block px-3 py-1 text-sm rounded-full mb-4
                @if($kelas->status == 'ongoing') bg-green-600
                @elseif($kelas->status == 'upcoming') bg-yellow-600
                @else bg-gray-600 @endif">
                {{ ucfirst($kelas->status) }}
            </span>

            <div class="space-y-4 text-gray-300">
                <div>
                    <span class="text-gray-500 text-sm">Instruktor:</span>
                    <p class="font-semibold text-lg">{{ $kelas->instruktor }}</p>
                </div>

                <div>
                    <span class="text-gray-500 text-sm">Deskripsi:</span>
                    <p class="mt-1">{{ $kelas->deskripsi ?? '-' }}</p>
                </div>

                <div class="pt-2 border-t border-gray-700">
                    <span class="text-gray-500 text-sm">Mulai:</span>
                    <p class="font-semibold">{{ $kelas->waktu_mulai->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <span class="text-gray-500 text-sm">Selesai:</span>
                    <p class="font-semibold">{{ $kelas->waktu_selesai->format('d M Y, H:i') }}</p>
                </div>

                <div class="pt-2 border-t border-gray-700">
                    <span class="text-gray-500 text-sm">Total Peserta:</span>
                    <p class="text-3xl font-bold text-blue-500">{{ $kelas->pengguna->count() }}</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-6">
                @if($isEnrolled)
                    <form action="{{ route('kelas.leave', $kelas) }}" method="POST" onsubmit="return confirm('Keluar dari kelas ini?')">
                        @csrf
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Keluar dari Kelas
                        </button>
                    </form>
                @else
                    <form action="{{ route('kelas.join', $kelas) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Gabung Kelas
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="lg:col-span-2">
        <div class="bg-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-bold mb-4">Daftar Peserta ({{ $kelas->pengguna->count() }})</h2>

            @if($kelas->pengguna->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">No</th>
                                <th class="text-left px-4 py-3">Nama</th>
                                <th class="text-left px-4 py-3">Email</th>
                                <th class="text-left px-4 py-3">Tgl Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->pengguna as $index => $peserta)
                                <tr class="border-t border-gray-700 hover:bg-gray-700 transition">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $peserta->nama_lengkap }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ $peserta->email }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">
                                        {{ $peserta->pivot->registration_date ? \Carbon\Carbon::parse($peserta->pivot->registration_date)->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-400 text-center py-8">Belum ada peserta yang bergabung dengan kelas ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection
