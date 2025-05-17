<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     *
     * @var string
     */
    protected $table = 'm_prodi';

    /**
     * Primary key tabel
     *
     * @var string
     */
    protected $primaryKey = 'kode_prodi';

    /**
     * Menentukan bahwa primary key bukan auto-increment
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Menentukan tipe data primary key
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi
     *
     * @var array
     */
    protected $fillable = [
        'kode_prodi',
        'nama_prodi'
    ];

    /**
     * Relasi dengan mahasiswa
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'program_studi_id', 'kode_prodi');
    }

    /**
     * Relasi dengan dosen
     */
    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'program_studi_id', 'kode_prodi');
    }
}