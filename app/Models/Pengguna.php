<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'alamat',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'pengguna_kelas', 'pengguna_id', 'kelas_id')
                    ->withPivot('registration_date')
                    ->withTimestamps();
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
