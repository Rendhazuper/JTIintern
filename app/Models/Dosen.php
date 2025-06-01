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
        'wilayah_id',
        'created_at',
        'updated_at',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // Relasi ke Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
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
    public function magangBimbingan()
    {
        return $this->hasMany(Magang::class, 'id_dosen', 'id_dosen');
    }
}
