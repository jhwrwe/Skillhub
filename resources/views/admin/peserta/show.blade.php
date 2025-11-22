@extends('layouts.app')

@section('title', 'Detail Peserta - SkillHub')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.peserta.index') }}" class="text-blue-500 hover:underline">← Kembali ke Daftar Peserta</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-gray-800 rounded-xl p-6">
            <h1 class="text-2xl font-bold mb-4">{{ $pengguna->nama_lengkap }}</h1>

            <span class="inline-block px-3 py-1 text-sm rounded-full mb-4
                @if($pengguna->role == 'admin') bg-purple-600 @else bg-green-600 @endif">
                {{ ucfirst($pengguna->role) }}
            </span>

            <div class="space-y-3 text-gray-300">
                <div>
                    <span class="text-gray-500">Email:</span>
                    <p>{{ $pengguna->email }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Alamat:</span>
                    <p>{{ $pengguna->alamat ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Terdaftar Sejak:</span>
                    <p>{{ $pengguna->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Total Kelas Diikuti:</span>
                    <p class="text-2xl font-bold text-blue-500">{{ $pengguna->kelas->count() }}</p>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <a href="{{ route('admin.peserta.edit', $pengguna) }}" class="bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-lg text-sm">Edit</a>
                @if($pengguna->id !== auth()->id())
                    <form action="{{ route('admin.peserta.destroy', $pengguna) }}" method="POST" onsubmit="return confirm('Yakin hapus peserta ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm">Hapus</button>
                    </form>
                @endif
            </div>
        </div>

        @if($availableKelas->count() > 0)
            <div class="bg-gray-800 rounded-xl p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Daftarkan ke Kelas</h3>
                <form action="{{ route('admin.peserta.daftar-kelas', $pengguna) }}" method="POST">
                    @csrf
                    <select name="kelas_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white mb-3">
                        @foreach($availableKelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ ucfirst($k->status) }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded-lg font-semibold">
                        Daftarkan
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        <div class="bg-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-bold mb-4">Kelas yang Diikuti</h2>

            @if($pengguna->kelas->count() > 0)
                <div class="space-y-4">
                    @foreach($pengguna->kelas as $kelas)
                        <div class="bg-gray-700 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold">{{ $kelas->nama_kelas }}</h3>
                                <p class="text-gray-400 text-sm">Instruktor: {{ $kelas->instruktor }}</p>
                                <p class="text-gray-500 text-xs">
                                    Terdaftar: {{ $kelas->pivot->registration_date ? \Carbon\Carbon::parse($kelas->pivot->registration_date)->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($kelas->status == 'ongoing') bg-green-600
                                    @elseif($kelas->status == 'upcoming') bg-yellow-600
                                    @else bg-gray-600 @endif">
                                    {{ ucfirst($kelas->status) }}
                                </span>
                                <form action="{{ route('admin.peserta.batal-kelas', [$pengguna, $kelas]) }}" method="POST" onsubmit="return confirm('Batalkan pendaftaran dari kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                                        Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-8">Peserta belum mengikuti kelas apapun.</p>
            @endif
        </div>
    </div>
</div>
@endsection
