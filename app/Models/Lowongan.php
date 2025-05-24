<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'm_lowongan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_lowongan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'judul',
        'id_perusahaan',
        'deskripsi',
        'persyaratan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'kuota',
        'gaji'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the perusahaan that owns the lowongan.
     */
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'perusahaan_id');
    }

    /**
     * Get the lamaran for the lowongan.
     */
    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_lowongan', 'id_lowongan');
    }
}
