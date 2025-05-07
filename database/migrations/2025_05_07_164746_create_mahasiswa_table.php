<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string('nim')->primary(); // Primary key NIM
            $table->foreignId('program_studi_id')->constrained('study_programs')->onDelete('cascade'); // Foreign key ke tabel study_programs
            $table->foreignId('skill_id')->nullable()->constrained('skills')->onDelete('set null'); // Foreign key ke tabel skills
            $table->string('alamat', 50)->nullable(); // Alamat mahasiswa
            $table->float('ipk', 3, 2)->nullable(); // IPK mahasiswa (contoh: 3.75)
            $table->string('telp', 25)->nullable(); // Nomor telepon mahasiswa
            $table->string('cv', 50)->nullable(); // Lokasi file CV mahasiswa
            $table->timestamps(); // Timestamps created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};