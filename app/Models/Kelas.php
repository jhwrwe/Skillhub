<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Kelas extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'kelas';

    //kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama_kelas',
        'deskripsi',
        'instruktor',
        'status',
        'waktu_mulai',
        'waktu_selesai',
    ];

    //menggunakan casting tipe data untuk klom waktu_mulai dan waktu_selesai untuk menjadi objek carbon
    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }
    //menggunakan boot method untuk otomatis menghitung status sebelum data disimpan
    protected static function boot()
    {
        parent::boot();

        //event ini akan dijalankan sebelum create atau update
        static::saving(function ($kelas) {
            $kelas->status = $kelas->calculateStatus();
        });
    }

    //menghitung status kelas berdasarkan waktu saat ini, returnnya akan diantara 'upcoming' atau 'ongoing' atau 'completed'
    public function calculateStatus(): string
    {
        $now = Carbon::now();

        //parsing waktu jika belum dalam format carbon
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

    //ini adalah acccessor untuk mendapatkan status terkini dari kelas, nanti akan dipanggil via $kelas->current_status
    public function getCurrentStatusAttribute(): string
    {
        return $this->calculateStatus();
    }

    //validasi apakah waktu_selesai lebih besar dari waktu_mulai
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

    //relasi many-to-many dengan Pengguna melalui pivot table pengguna_kelas, dikarenakan relasi satu kelas memiliki banyak peserta
    public function pengguna()
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_kelas', 'kelas_id', 'pengguna_id')
                    ->withPivot('registration_date')
                    ->withTimestamps();
    }

    //scope ini akan mengfilter kelas yang sedang berlangsung
    public function scopeOngoing($query)
    {
        $now = Carbon::now();
        return $query->where('waktu_mulai', '<=', $now)
                     ->where('waktu_selesai', '>=', $now);
    }

    //scope ini akan mengfilter yang akan datang
    public function scopeUpcoming($query)
    {
        return $query->where('waktu_mulai', '>', Carbon::now());
    }

    //scope ini akan mengfilter yang sudah selesai
    public function scopeCompleted($query)
    {
        return $query->where('waktu_selesai', '<', Carbon::now());
    }

    //mengambil semua kelas dengan status terkini
    public function scopeWithCurrentStatus($query)
    {
        return $query->get()->map(function ($kelas) {
            $kelas->status = $kelas->calculateStatus();
            return $kelas;
        });
    }
}
