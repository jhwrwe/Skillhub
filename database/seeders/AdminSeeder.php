<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::create([
            'nama_lengkap' => 'Admin SkillHub',
            'email' => 'admin@skillhub.com',
            'password' => Hash::make('password123'),
            'alamat' => 'Jakarta, Indonesia',
            'role' => 'admin',
        ]);

        Pengguna::create([
            'nama_lengkap' => 'John Doe',
            'email' => 'john@student.com',
            'password' => Hash::make('password123'),
            'alamat' => 'Bandung, Indonesia',
            'role' => 'student',
        ]);
    }
}
