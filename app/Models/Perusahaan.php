<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'm_perusahaan';
    protected $primaryKey = 'perusahaan_id';

    protected $fillable = [
        'nama_perusahaan',
        'alamat_perusahaan',
        'kota',
        'contact_person',
        'email',
        'instagram',
        'website'
    ];

    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'perusahaan_id', 'perusahaan_id');
    }
}