<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Relasi ke Pengguna (students yang join)
    public function pengguna()
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_kelas', 'kelas_id', 'pengguna_id')
                    ->withPivot('registration_date')
                    ->withTimestamps();
    }

    // Scope untuk filter by status
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
