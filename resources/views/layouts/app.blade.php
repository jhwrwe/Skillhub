<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SkillHub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-blue-500">SkillHub</a>

                    @auth
                        <div class="ml-10 flex space-x-4">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:text-white px-3 py-2">Dashboard</a>
                                <a href="{{ route('admin.kelas.index') }}" class="text-gray-300 hover:text-white px-3 py-2">Kelola Kelas</a>
                                <a href="{{ route('admin.peserta.index') }}" class="text-gray-300 hover:text-white px-3 py-2">Kelola Peserta</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white px-3 py-2">Dashboard</a>
                                <a href="{{ route('kelas.index') }}" class="text-gray-300 hover:text-white px-3 py-2">Semua Kelas</a>
                                <a href="{{ route('kelas.my') }}" class="text-gray-300 hover:text-white px-3 py-2">Kelas Saya</a>
                            @endif
                        </div>
                    @endauth
                </div>

                <div class="flex items-center">
                    @auth
                        <span class="text-gray-300 mr-4">{{ auth()->user()->nama_lengkap }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-4 py-2">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg ml-2">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-600 text-white px-4 py-3 rounded-lg">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-600 text-white px-4 py-3 rounded-lg">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
</body>
</html>
