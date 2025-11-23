@extends('layouts.app')

@section('title', 'Detail Kelas - SkillHub')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.kelas.index') }}" class="text-blue-500 hover:underline">← Kembali ke Daftar Kelas</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-gray-800 rounded-xl p-6 mb-6">
            <h1 class="text-2xl font-bold mb-4">{{ $kelas->nama_kelas }}</h1>

            <span class="inline-block px-3 py-1 text-sm rounded-full mb-4 font-semibold
                @if($kelas->status == 'ongoing') bg-green-600 text-white
                @elseif($kelas->status == 'upcoming') bg-yellow-600 text-white
                @else bg-gray-600 text-white @endif">
                {{ ucfirst($kelas->status) }}
            </span>

            <div class="space-y-4 text-gray-300">
                <div>
                    <span class="text-gray-500 text-sm">Instruktor:</span>
                    <p class="font-semibold text-base">{{ $kelas->instruktor }}</p>
                </div>

                <div>
                    <span class="text-gray-500 text-sm">Deskripsi:</span>
                    <p class="text-sm mt-1">{{ $kelas->deskripsi ?? '-' }}</p>
                </div>

                <div class="pt-2 border-t border-gray-700">
                    <span class="text-gray-500 text-sm">Waktu Mulai:</span>
                    <p class="font-semibold">{{ $kelas->waktu_mulai->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <span class="text-gray-500 text-sm">Waktu Selesai:</span>
                    <p class="font-semibold">{{ $kelas->waktu_selesai->format('d M Y, H:i') }}</p>
                </div>

                <div class="pt-2 border-t border-gray-700">
                    <span class="text-gray-500 text-sm">Total Peserta:</span>
                    <p class="text-3xl font-bold text-blue-500">{{ $kelas->pengguna->count() }}</p>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <a href="{{ route('admin.kelas.edit', $kelas) }}"
                   class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition text-center">
                    ✏️ Edit
                </a>
                <form action="{{ route('admin.kelas.destroy', $kelas) }}" method="POST"
                      onsubmit="return confirm('Yakin hapus kelas ini? Semua peserta akan dihapus juga.')"
                      class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>

        @if($availablePeserta->count() > 0)
            <div class="bg-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">➕ Tambah Peserta</h3>
                <form action="{{ route('admin.kelas.daftarkan-peserta', $kelas) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="pengguna_id" class="block text-gray-300 text-sm mb-2">Pilih Peserta:</label>
                        <select name="pengguna_id" id="pengguna_id"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white mb-3 focus:outline-none focus:border-blue-500"
                                required>
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($availablePeserta as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->nama_lengkap }} ({{ $p->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Daftarkan Peserta
                    </button>
                </form>

                @if($errors->has('pengguna_id'))
                    <div class="mt-2 bg-red-600/20 border border-red-600 text-red-400 px-3 py-2 rounded text-sm">
                        {{ $errors->first('pengguna_id') }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-gray-800 rounded-xl p-6">
                <p class="text-gray-400 text-center py-8">
                    ✓ Semua peserta sudah terdaftar di kelas ini.
                </p>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        <div class="bg-gray-800 rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">👥 Daftar Peserta ({{ $kelas->pengguna->count() }})</h2>
            </div>

            @if($kelas->pengguna->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold">No</th>
                                <th class="text-left px-4 py-3 font-semibold">Nama Lengkap</th>
                                <th class="text-left px-4 py-3 font-semibold">Email</th>
                                <th class="text-left px-4 py-3 font-semibold">Tgl Daftar</th>
                                <th class="text-center px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelas->pengguna as $index => $peserta)
                                <tr class="border-t border-gray-700 hover:bg-gray-700 transition">
                                    <td class="px-4 py-3">
                                        <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded text-xs font-semibold">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold">{{ $peserta->nama_lengkap }}</td>
                                    <td class="px-4 py-3 text-gray-400">{{ $peserta->email }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">
                                        {{ $peserta->pivot->registration_date ? \Carbon\Carbon::parse($peserta->pivot->registration_date)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('admin.kelas.remove-peserta', [$kelas, $peserta]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Keluarkan peserta ini dari kelas?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold transition">
                                                Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-700">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-gray-400 text-xs">Total Peserta</p>
                            <p class="text-2xl font-bold text-blue-400">{{ $kelas->pengguna->count() }}</p>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-3">
                            <p class="text-gray-400 text-xs">Kapasitas Tersisa</p>
                            <p class="text-2xl font-bold text-green-400">
                                {{ 100 - $kelas->pengguna->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-400 text-lg mb-4">Belum ada peserta yang mendaftar ke kelas ini.</p>
                    <p class="text-gray-500 text-sm">Gunakan form di sebelah kiri untuk menambahkan peserta.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        window.addEventListener('load', function() {
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563EB'
            });
        });
    </script>
@endif

@endsection
