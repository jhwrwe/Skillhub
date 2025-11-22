@extends('layouts.app')

@section('title', 'Tambah Kelas - SkillHub')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Tambah Kelas Baru</h1>

    <div class="bg-gray-800 rounded-xl p-8">
        <form action="{{ route('admin.kelas.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="nama_kelas" class="block text-gray-300 mb-2">Nama Kelas</label>
                <input type="text" name="nama_kelas" id="nama_kelas" value="{{ old('nama_kelas') }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('nama_kelas')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="block text-gray-300 mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="instruktor" class="block text-gray-300 mb-2">Instruktor</label>
                <input type="text" name="instruktor" id="instruktor" value="{{ old('instruktor') }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('instruktor')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-300 mb-2">Status</label>
                <select name="status" id="status"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="waktu_mulai" class="block text-gray-300 mb-2">Waktu Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" value="{{ old('waktu_mulai') }}"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                        required>
                    @error('waktu_mulai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="waktu_selesai" class="block text-gray-300 mb-2">Waktu Selesai</label>
                    <input type="datetime-local" name="waktu_selesai" id="waktu_selesai" value="{{ old('waktu_selesai') }}"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                        required>
                    @error('waktu_selesai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Simpan Kelas
                </button>
                <a href="{{ route('admin.kelas.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
