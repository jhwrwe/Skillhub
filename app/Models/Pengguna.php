<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengguna extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table = 'pengguna';

    //kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'alamat',
        'role',
    ];
    //kolom ini akan disembunyikan saat serialisasi
    protected $hidden = [
        'password',
    ];

    //menggunakan casting tipe data untuk kolom password supaya akan di 'hash' secara otomatis waktu disimpan
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    //relasi many-to-many dengan kelas melalui pivot table pengguna_kelas dikarenakan relasinya memang satu pengguna itu dapat mengikuti banyak kelas
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'pengguna_kelas', 'pengguna_id', 'kelas_id')
                    ->withPivot('registration_date')
                    ->withTimestamps();
    }

    //cek apakah pengguna adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    //cek apakah pengguna adalah student
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
