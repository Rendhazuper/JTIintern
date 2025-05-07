<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LowonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lowongan')->insert([
            [
                'perusahaan_id' => 1, // Pastikan perusahaan dengan ID 1 ada di tabel companies
                'judul_lowongan' => 'Software Engineer Intern',
                'kapasitas' => 5,
                'deskripsi' => 'Bertanggung jawab untuk membantu pengembangan aplikasi web.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'perusahaan_id' => 2, // Pastikan perusahaan dengan ID 2 ada di tabel companies
                'judul_lowongan' => 'UI/UX Designer Intern',
                'kapasitas' => 3,
                'deskripsi' => 'Membantu tim desain dalam membuat prototipe dan desain antarmuka.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'perusahaan_id' => 3, // Pastikan perusahaan dengan ID 3 ada di tabel companies
                'judul_lowongan' => 'Data Analyst Intern',
                'kapasitas' => 2,
                'deskripsi' => 'Menganalisis data untuk mendukung pengambilan keputusan bisnis.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}