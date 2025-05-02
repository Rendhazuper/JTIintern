<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat user admin
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Ganti 'password' dengan password yang lebih kuat
            'role' => 'admin',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat user dosen
        DB::table('users')->insert([
            'name' => 'Dosen User',
            'email' => 'dosen@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),  // Ganti 'password' dengan password yang lebih kuat
            'role' => 'dosen',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Membuat user mahasiswa
        DB::table('users')->insert([
            'name' => 'Mahasiswa User',
            'email' => 'mahasiswa@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),  // Ganti 'password' dengan password yang lebih kuat
            'role' => 'mahasiswa',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
