<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TSkillMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_skill_mahasiswa')->insert([
            [
                'user_id' => 3, // Pastikan user dengan ID 1 ada di tabel m_user
                'skill_id' => 1, // Pastikan skill dengan ID 1 ada di tabel m_skill
                'lama_skill' => 12, // Lama skill dalam bulan
            ],
        ]);
    }
}