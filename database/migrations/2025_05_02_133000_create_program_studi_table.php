<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Reverses the migration.
     */
    /*******  4f71bdc1-fa2b-4dfc-8c38-2e122c328c9e  *******/    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
