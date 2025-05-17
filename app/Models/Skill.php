<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'm_skill';
    protected $primaryKey = 'skill_id';

    protected $fillable = [
        'nama_skill',
        'kategori_id',
        'deskripsi'
    ];

    /**
     * Mendapatkan mahasiswa yang memiliki skill ini
     */
    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 't_skill_mahasiswa', 'skill_id', 'user_id');
    }

    /**
     * Mendapatkan lowongan yang memerlukan skill ini
     */
    public function lowongan()
    {
        return $this->belongsToMany(Lowongan::class, 't_skill_lowongan', 'skill_id', 'id_lowongan');
    }
}