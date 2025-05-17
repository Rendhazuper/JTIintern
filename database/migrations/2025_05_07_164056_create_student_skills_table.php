<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_skills', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('m_users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade'); // Foreign key ke tabel skills
            $table->integer('lama_skill')->nullable(); // Lama skill dalam satuan waktu (misalnya bulan/tahun)
            $table->primary(['user_id', 'skill_id']); // Composite primary key
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_skills');
    }
};