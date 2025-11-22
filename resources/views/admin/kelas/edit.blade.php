@extends('layouts.app')

@section('title', 'Edit Kelas - SkillHub')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Edit Kelas</h1>

    <div class="bg-gray-800 rounded-xl p-8">
        <!-- Info Box -->
        <div class="bg-blue-600/20 border border-blue-600 text-blue-400 px-4 py-3 rounded-lg mb-6">
            <p class="text-sm">ℹ️ <strong>Status kelas akan otomatis diperbarui</strong> berdasarkan tanggal:</p>
            <ul class="text-xs mt-2 ml-4 list-disc">
                <li><strong>Upcoming:</strong> Jika waktu mulai belum tiba</li>
                <li><strong>Ongoing:</strong> Jika kelas sedang berlangsung</li>
                <li><strong>Completed:</strong> Jika waktu selesai sudah lewat</li>
            </ul>
        </div>

        <!-- Current Status Display -->
        <div class="mb-6 p-4 bg-gray-700 rounded-lg">
            <p class="text-gray-300 text-sm mb-2">Status saat ini:</p>
            @php
                $currentStatus = $kelas->calculateStatus();
                $statusClasses = [
                    'upcoming' => 'bg-yellow-600 text-white',
                    'ongoing' => 'bg-green-600 text-white',
                    'completed' => 'bg-gray-600 text-white'
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full font-semibold {{ $statusClasses[$currentStatus] }}">
                {{ ucfirst($currentStatus) }}
            </span>
        </div>

        <form action="{{ route('admin.kelas.update', $kelas) }}" method="POST" id="kelasForm">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nama_kelas" class="block text-gray-300 mb-2">Nama Kelas</label>
                <input type="text" name="nama_kelas" id="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('nama_kelas')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="block text-gray-300 mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">{{ old('deskripsi', $kelas->deskripsi) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="instruktor" class="block text-gray-300 mb-2">Instruktor</label>
                <input type="text" name="instruktor" id="instruktor" value="{{ old('instruktor', $kelas->instruktor) }}"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                    required>
                @error('instruktor')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="waktu_mulai" class="block text-gray-300 mb-2">Waktu Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" id="waktu_mulai"
                        value="{{ old('waktu_mulai', $kelas->waktu_mulai->format('Y-m-d\TH:i')) }}"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                        required>
                    @error('waktu_mulai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="waktu_selesai" class="block text-gray-300 mb-2">Waktu Selesai</label>
                    <input type="datetime-local" name="waktu_selesai" id="waktu_selesai"
                        value="{{ old('waktu_selesai', $kelas->waktu_selesai->format('Y-m-d\TH:i')) }}"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                        required>
                    @error('waktu_selesai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status Preview -->
            <div id="statusPreview" class="mb-6 p-4 bg-gray-700 rounded-lg">
                <p class="text-gray-300 text-sm mb-2">Status setelah update:</p>
                <span id="statusBadge" class="px-3 py-1 text-sm rounded-full font-semibold"></span>
                <p id="statusExplanation" class="text-xs text-gray-400 mt-2"></p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Update Kelas
                </button>
                <a href="{{ route('admin.kelas.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const waktuMulai = document.getElementById('waktu_mulai');
    const waktuSelesai = document.getElementById('waktu_selesai');
    const statusPreview = document.getElementById('statusPreview');
    const statusBadge = document.getElementById('statusBadge');
    const statusExplanation = document.getElementById('statusExplanation');

    function updateStatusPreview() {
        if (!waktuMulai.value || !waktuSelesai.value) {
            return;
        }

        const start = new Date(waktuMulai.value);
        const end = new Date(waktuSelesai.value);
        const now = new Date();

        let status, statusClass, explanation;

        if (now < start) {
            status = 'Upcoming';
            statusClass = 'bg-yellow-600 text-white';
            explanation = `Kelas belum dimulai. Akan mulai pada ${start.toLocaleString('id-ID')}`;
        } else if (now >= start && now <= end) {
            status = 'Ongoing';
            statusClass = 'bg-green-600 text-white';
            explanation = `Kelas sedang berlangsung. Dimulai ${start.toLocaleString('id-ID')} dan berakhir ${end.toLocaleString('id-ID')}`;
        } else {
            status = 'Completed';
            statusClass = 'bg-gray-600 text-white';
            explanation = `Kelas sudah selesai pada ${end.toLocaleString('id-ID')}`;
        }

        statusBadge.textContent = status;
        statusBadge.className = `px-3 py-1 text-sm rounded-full font-semibold ${statusClass}`;
        statusExplanation.textContent = explanation;
    }

    updateStatusPreview();

    waktuMulai.addEventListener('change', updateStatusPreview);
    waktuSelesai.addEventListener('change', updateStatusPreview);
    waktuMulai.addEventListener('input', updateStatusPreview);
    waktuSelesai.addEventListener('input', updateStatusPreview);

    document.getElementById('kelasForm').addEventListener('submit', function(e) {
        const start = new Date(waktuMulai.value);
        const end = new Date(waktuSelesai.value);

        if (end <= start) {
            e.preventDefault();
            alert('Waktu selesai harus setelah waktu mulai!');
        }
    });
</script>
@endsection
