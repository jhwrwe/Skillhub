<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        $startDate = Carbon::now()->addDays(rand(-5, 10));
        $endDate = (clone $startDate)->addDays(rand(1, 7));

        return [
            'nama_kelas' => fake()->randomElement([
                'Desain Grafis',
                'Pemrograman Dasar',
                'Editing Video',
                'Public Speaking',
                'Laravel Framework',
                'React JS',
                'Digital Marketing',
            ]) . ' ' . fake()->numberBetween(1, 100),
            'deskripsi' => fake()->paragraph(),
            'instruktor' => fake()->name(),
            'waktu_mulai' => $startDate,
            'waktu_selesai' => $endDate,
            'status' => 'upcoming',
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'waktu_mulai' => Carbon::now()->addDays(5),
            'waktu_selesai' => Carbon::now()->addDays(10),
            'status' => 'upcoming',
        ]);
    }

    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'waktu_mulai' => Carbon::now()->subDays(2),
            'waktu_selesai' => Carbon::now()->addDays(5),
            'status' => 'ongoing',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'waktu_mulai' => Carbon::now()->subDays(10),
            'waktu_selesai' => Carbon::now()->subDays(5),
            'status' => 'completed',
        ]);
    }
}
