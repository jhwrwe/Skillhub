@extends('layouts.app')

@section('title', 'Login - SkillHub')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-gray-800 rounded-xl p-8">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-300 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-300 mb-2">Password</label>
                <input type="password" name="password" id="password"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                Login
            </button>
        </form>

        <p class="text-center text-gray-400 mt-4">
            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Register</a>
        </p>
    </div>
</div>
@endsection
