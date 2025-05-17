<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'm_mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'user_id',
        'nama_mahasiswa',
        'program_studi_id',
        'semester',
        'alamat',
        'no_telepon',
        'email',
        'dosen_pembimbing_id',
        'skills'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function programStudi()
    {
        return $this->belongsTo(Prodi::class, 'program_studi_id', 'kode_prodi');
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id', 'id_dosen');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 't_skill_mahasiswa', 'user_id', 'skill_id', 'user_id')
                    ->withPivot('level', 'sertifikasi')
                    ->withTimestamps();
    }
}