<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            [
                'nama_kelas' => 'Desain Grafis',
                'deskripsi' => 'Pelajari fundamental desain grafis, komposisi, tipografi, dan penggunaan software design profesional.',
                'instruktor' => 'Budi Santoso',
                'waktu_mulai' => Carbon::now()->addDays(2)->setTime(9, 0),
                'waktu_selesai' => Carbon::now()->addDays(16)->setTime(17, 0),
            ],
            [
                'nama_kelas' => 'Pemrograman Dasar',
                'deskripsi' => 'Kursus intensif pemrograman dasar menggunakan PHP dan JavaScript untuk pemula.',
                'instruktor' => 'Ahmad Wijaya',
                'waktu_mulai' => Carbon::now()->subDays(5)->setTime(10, 0),
                'waktu_selesai' => Carbon::now()->addDays(10)->setTime(16, 0),
            ],
            [
                'nama_kelas' => 'Editing Video',
                'deskripsi' => 'Master video editing dengan Adobe Premiere Pro dan DaVinci Resolve untuk konten profesional.',
                'instruktor' => 'Siti Nurhaliza',
                'waktu_mulai' => Carbon::now()->addDays(5)->setTime(14, 0),
                'waktu_selesai' => Carbon::now()->addDays(19)->setTime(18, 0),
            ],
            [
                'nama_kelas' => 'Public Speaking',
                'deskripsi' => 'Tingkatkan kemampuan presentasi dan komunikasi publik dengan teknik-teknik yang terbukti efektif.',
                'instruktor' => 'Rini Handoko',
                'waktu_mulai' => Carbon::now()->subDays(10)->setTime(11, 0),
                'waktu_selesai' => Carbon::now()->subDays(2)->setTime(15, 0),
            ],
        ];

        foreach ($kelas as $k) {
            Kelas::create($k);
        }
    }
}
