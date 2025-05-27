<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'm_dosen';
    protected $primaryKey = 'id_dosen';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        // 'user_id',
        // 'nip',
        // 'nama_dosen',
        // 'wilayah_id',
        // 'perusahaan_id', // Tambah ini
        // 'alamat',
        // 'nomor_telepon',
        // 'email'

        'user_id',
        'wilayah_id',
        'nip',
    ];

    /**
     * Mendapatkan user yang terkait dengan dosen
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }


    /**
     * Mendapatkan mahasiswa bimbingan
     */
    public function mahasiswaBimbingan()
    {
        return $this->hasMany(Mahasiswa::class, 'dosen_pembimbing_id', 'nip');
    }

    /**
     * Mendapatkan lamaran yang disetujui dosen
     */
    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_dosen', 'nip');
    }

    /**
     * Mendapatkan wilayah dosen
     */
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }
}