@extends('layouts.app')

@section('title', 'Register - SkillHub')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-gray-800 rounded-xl p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Register</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="nama_lengkap" class="block text-gray-300 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('nama_lengkap')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-300 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="alamat" class="block text-gray-300 mb-2">Alamat (Opsional)</label>
                <textarea name="alamat" id="alamat" rows="2"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">{{ old('alamat') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-300 mb-2">Password</label>
                <input type="password" name="password" id="password"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-300 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                Register
            </button>
        </form>

        <p class="text-center text-gray-400 mt-4">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
