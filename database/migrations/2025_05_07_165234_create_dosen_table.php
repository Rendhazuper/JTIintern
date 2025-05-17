<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('m_users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->string('nip')->primary(); // Primary key NIP
            $table->timestamps(); // Timestamps created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};