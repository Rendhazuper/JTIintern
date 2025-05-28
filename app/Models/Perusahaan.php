<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'm_perusahaan';
    protected $primaryKey = 'perusahaan_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_perusahaan',
        'alamat_perusahaan',
        'wilayah_id',
        'contact_person',
        'email',
        'instagram',
        'website',
        'deskripsi',
        'gmaps',
    ];

    // Relasi ke tabel m_wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    // Relasi ke tabel t_lowongan
    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'perusahaan_id', 'perusahaan_id');
    }
}