<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'm_wilayah';
    protected $primaryKey = 'wilayah_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_kota',
    ];

    public function perusahaan()
    {
        return $this->hasMany(Perusahaan::class, 'wilayah_id', 'wilayah_id');
    }
}