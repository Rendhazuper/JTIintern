<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_lowongan', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign('m_lowongan_skill_id_foreign');
            
            // Then drop the column
            $table->dropColumn('skill_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_lowongan', function (Blueprint $table) {
            // Add back the column
            $table->unsignedBigInteger('skill_id')->nullable();
            
            // Add back the foreign key if needed
            $table->foreign('skill_id')->references('skill_id')->on('m_skill')->onDelete('set null');
        });
    }
};