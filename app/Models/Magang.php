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
        'id_perusahaan',
        'posisi',
        'status',
        'surat_url',
        'cv_url',
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
        return $this->hasManyThrough(
            Lamaran::class,  // Model tujuan
            Lowongan::class, // Model perantara
            'id_lowongan',   // Foreign key di tabel `m_magang` yang merujuk ke `lowongan`
            'id_lowongan',   // Foreign key di tabel `t_lamaran` yang merujuk ke `lowongan`
            'id_lowongan',   // Local key di tabel `m_magang`
            'id_lowongan'    // Local key di tabel `t_lamaran`
        );
    }
}
