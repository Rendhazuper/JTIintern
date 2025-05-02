<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('skills')->insert([
            ['name' => 'PHP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'JavaScript', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'UI/UX Design', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Project Management', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Data Analysis', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}