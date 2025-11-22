@extends('layouts.app')

@section('title', 'Kelola Peserta - SkillHub')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Kelola Peserta</h1>
    <a href="{{ route('admin.peserta.create') }}" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-semibold">
        + Tambah Peserta
    </a>
</div>

<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-700">
            <tr>
                <th class="text-left px-6 py-3">No</th>
                <th class="text-left px-6 py-3">Nama Lengkap</th>
                <th class="text-left px-6 py-3">Email</th>
                <th class="text-left px-6 py-3">Role</th>
                <th class="text-left px-6 py-3">Course Diikuti</th>
                <th class="text-left px-6 py-3">Tgl Daftar</th>
                <th class="text-left px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peserta as $index => $p)
                <tr class="border-t border-gray-700 hover:bg-gray-700 transition">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-semibold">{{ $p->nama_lengkap }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $p->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($p->role == 'admin') bg-purple-600 text-white @else bg-green-600 text-white @endif">
                            {{ ucfirst($p->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="bg-blue-600 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $p->kelas_count }}
                            </span>
                            <span class="text-gray-400 text-sm">
                                @if($p->kelas_count == 0)
                                    Belum ada
                                @elseif($p->kelas_count == 1)
                                    course
                                @else
                                    courses
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.peserta.show', $p) }}"
                               class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm font-semibold transition">
                                Detail
                            </a>
                            <a href="{{ route('admin.peserta.edit', $p) }}"
                               class="bg-yellow-600 hover:bg-yellow-700 px-3 py-1 rounded text-sm font-semibold transition">
                                Edit
                            </a>
                            @if($p->id !== auth()->id())
                                <form action="{{ route('admin.peserta.destroy', $p) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus peserta ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm font-semibold transition">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                        Belum ada peserta terdaftar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
