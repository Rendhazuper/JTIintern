<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mahasiswa')->insert([
            [
                'user_id' => 3,
                'nim' => '987654321',
                'program_studi_id' => 2,
                'skill_id' => 2,
                'alamat' => 'Jl. Sudirman No. 2, Jakarta',
                'ipk' => 3.50,
                'telp' => '081298765432',
                'cv' => 'uploads/cv/cv_987654321.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}