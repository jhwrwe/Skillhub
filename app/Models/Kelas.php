<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'deskripsi',
        'instruktor',
        'status',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($kelas) {
            $kelas->status = $kelas->calculateStatus();
        });
    }

    public function calculateStatus(): string
    {
        $now = Carbon::now();

        $waktuMulai = $this->waktu_mulai instanceof Carbon
            ? $this->waktu_mulai
            : Carbon::parse($this->waktu_mulai);

        $waktuSelesai = $this->waktu_selesai instanceof Carbon
            ? $this->waktu_selesai
            : Carbon::parse($this->waktu_selesai);

        if ($now->lt($waktuMulai)) {
            return 'upcoming';
        }

        if ($now->gte($waktuMulai) && $now->lte($waktuSelesai)) {
            return 'ongoing';
        }

        return 'completed';
    }


    public function getCurrentStatusAttribute(): string
    {
        return $this->calculateStatus();
    }


    public function hasValidDates(): bool
    {
        $waktuMulai = $this->waktu_mulai instanceof Carbon
            ? $this->waktu_mulai
            : Carbon::parse($this->waktu_mulai);

        $waktuSelesai = $this->waktu_selesai instanceof Carbon
            ? $this->waktu_selesai
            : Carbon::parse($this->waktu_selesai);

        return $waktuSelesai->gt($waktuMulai);
    }

    public function pengguna()
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_kelas', 'kelas_id', 'pengguna_id')
                    ->withPivot('registration_date')
                    ->withTimestamps();
    }

    public function scopeOngoing($query)
    {
        $now = Carbon::now();
        return $query->where('waktu_mulai', '<=', $now)
                     ->where('waktu_selesai', '>=', $now);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('waktu_mulai', '>', Carbon::now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('waktu_selesai', '<', Carbon::now());
    }


    public function scopeWithCurrentStatus($query)
    {
        return $query->get()->map(function ($kelas) {
            $kelas->status = $kelas->calculateStatus();
            return $kelas;
        });
    }
}
