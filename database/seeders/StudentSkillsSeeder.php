<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_skills')->insert([
            ['user_id' => 3, 'skill_id' => 1, 'lama_skill' => 12],
            ['user_id' => 3, 'skill_id' => 2, 'lama_skill' => 6],
            ['user_id' => 3, 'skill_id' => 3, 'lama_skill' => 24],
        ]);
    }
}