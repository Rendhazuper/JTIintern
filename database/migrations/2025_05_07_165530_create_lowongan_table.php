<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('companies')->onDelete('cascade'); // Foreign key ke tabel companies
            $table->string('judul_lowongan', 50); // Judul lowongan
            $table->integer('kapasitas'); // Kapasitas lowongan
            $table->text('deskripsi')->nullable(); // Deskripsi lowongan
            $table->timestamps(); // Timestamps created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongan');
    }
};