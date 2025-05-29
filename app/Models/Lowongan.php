<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    use HasFactory;

    protected $table = 'm_lowongan'; // Nama tabel
    protected $primaryKey = 'id_lowongan'; // Kolom ID utama
    public $timestamps = true; // Jika tabel memiliki kolom created_at dan updated_at

    protected $fillable = [
        'judul_lowongan',
        'perusahaan_id',
        'periode_id',
        'kapasitas',
        'deskripsi',
        'skill_id',
        'jenis_id',
    ];

    // Relasi ke model Perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'perusahaan_id');
    }

    // Relasi ke model Periode
    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', 'periode_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id', 'jenis_id');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_lowongan', 'id_lowongan');
    }
}
