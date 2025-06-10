<?php
// filepath: app/Models/Dosen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'm_dosen';
    protected $primaryKey = 'id_dosen';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nip',
        'no_hp',
        'alamat',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // Relasi ke Skills
    public function skills()
    {
        return $this->hasMany(SkillDosen::class, 'id_dosen', 'id_dosen');
    }

    // Relasi ke Perusahaan
    public function perusahaan()
    {
        return $this->belongsToMany(Perusahaan::class, 't_dosen_perusahaan', 'id_dosen', 'perusahaan_id');
    }

    // Relasi ke Beban Kerja
    public function workload()
    {
        return $this->hasOne(DosenBebanKerja::class, 'id_dosen', 'id_dosen');
    }

    // Relasi ke Magang yang dibimbing
    public function magang_bimbingan()
    {
        return $this->hasMany(Magang::class, 'id_dosen', 'id_dosen');
    }

    public function minat()
    {
        return $this->belongsToMany(Minat::class, 't_minat_dosen', 'dosen_id', 'minat_id');
    }
}
