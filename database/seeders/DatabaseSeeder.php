<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() :void
    {
        // Panggil semua seeder yang diperlukan
        $this->call([
            UserSeeder::class,
            DosenSeeder::class,
             ProgramStudiSeeder::class,
              SkillsSeeder::class,
            MahasiswaSeeder::class,
            DocumentSeeder::class,
            CompaniesSeeder::class,
            LowonganSeeder::class,
            StudentSkillsSeeder::class,
        ]);
    }
}
