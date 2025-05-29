<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magang extends Model
{
    use HasFactory;

    protected $table = 'm_magang';
    protected $primaryKey = 'id_magang';

    protected $fillable = [
        'id_mahasiswa',
        'id_lowongan',  // Tambahkan ini
        'id_dosen',     // Tambahkan ini
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'id_lowongan', 'id_lowongan');
    }

    public function dokumen()
    {
        return $this->hasOneThrough(Dokumen::class, Mahasiswa::class, 'id_mahasiswa', 'id_user', 'id_mahasiswa', 'id_user');
    }

    public function lamaran()
    {
        // Jika masih ingin menggunakan relasi ini, perbaiki parameter-nya
        return $this->hasManyThrough(
            Lamaran::class,
            Lowongan::class,
            'id_lowongan', // FK di Lowongan model yang terhubung ke Magang
            'id_lowongan', // FK di Lamaran model yang terhubung ke Lowongan
            'id_lowongan', // PK di Magang
            'id_lowongan'  // PK di Lowongan
        );
    }
}
