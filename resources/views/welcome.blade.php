<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillHub - Tailwind Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">

    <div class="text-center">
        <h1 class="text-5xl font-bold text-white mb-4">SkillHub</h1>
        <p class="text-gray-400 mb-6">Tailwind CSS is working!</p>

        <div class="space-x-4">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                Button 1
            </button>
            <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                Button 2
            </button>
        </div>

        <div class="mt-8 p-6 bg-gray-800 rounded-xl max-w-md mx-auto">
            <p class="text-gray-300">Kalau box ini punya rounded corners dan background abu-abu, berarti Tailwind works! ✅</p>
        </div>
    </div>

</body>
</html>
