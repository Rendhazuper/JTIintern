<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MMahasiswaSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_mahasiswa')->insert([
            [
                'id_user'    => 3, // Pastikan id_user=3 ada di m_user
                'kode_prodi' => 'TI', // Pastikan kode_prodi ini ada di m_prodi
                'skill_id'   => 2, // Pastikan skill_id ini ada di m_skill
                'jenis_id'   => 1, // Pastikan jenis_id ini ada di m_jenis
                'nim'        => 12345678,
                'alamat'     => 'Jl. Merdeka No. 1',
                'ipk'        => 3.75,
                'telp'       => '081234567890',
                'cv'         => 'cv_mahasiswa1.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}