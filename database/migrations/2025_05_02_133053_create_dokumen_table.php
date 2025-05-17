<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('m_users')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // e.g., 'CV', 'Surat Pengantar', 'Sertifikat'
            $table->text('description')->nullable();
            $table->timestamp('upload_date')->useCurrent();
            $table->timestamps(); // Optional: if you need created_at/updated_at for the record itself
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
