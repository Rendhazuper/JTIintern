<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('study_programs')->insert([
            ['name' => 'D4 Teknik Informatika', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'D3 Manajemen Informatika', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'D4 Sistem Informasi Bisnis', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
