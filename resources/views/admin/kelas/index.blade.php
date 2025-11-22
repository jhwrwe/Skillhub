@extends('layouts.app')

@section('title', 'Kelola Kelas - SkillHub')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Kelola Kelas</h1>
    <a href="{{ route('admin.kelas.create') }}" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-semibold">
        + Tambah Kelas
    </a>
</div>

<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-700">
            <tr>
                <th class="text-left px-6 py-3">Nama Kelas</th>
                <th class="text-left px-6 py-3">Instruktor</th>
                <th class="text-left px-6 py-3">Status</th>
                <th class="text-left px-6 py-3">Peserta Terdaftar</th>
                <th class="text-left px-6 py-3">Waktu Mulai</th>
                <th class="text-left px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelas as $k)
                <tr class="border-t border-gray-700 hover:bg-gray-700 transition">
                    <td class="px-6 py-4 font-semibold">{{ $k->nama_kelas }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $k->instruktor }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                            @if($k->status == 'ongoing') bg-green-600 text-white
                            @elseif($k->status == 'upcoming') bg-yellow-600 text-white
                            @else bg-gray-600 text-white @endif">
                            {{ ucfirst($k->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full font-bold text-sm">
                                {{ $k->pengguna_count }}
                            </span>
                            <span class="text-gray-400">
                                @if($k->pengguna_count == 0)
                                    peserta
                                @elseif($k->pengguna_count == 1)
                                    peserta
                                @else
                                    peserta
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm">
                        {{ $k->waktu_mulai->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.kelas.show', $k) }}"
                               class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm font-semibold transition">
                                Detail
                            </a>
                            <a href="{{ route('admin.kelas.edit', $k) }}"
                               class="bg-yellow-600 hover:bg-yellow-700 px-3 py-1 rounded text-sm font-semibold transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus kelas ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm font-semibold transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        Belum ada kelas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
