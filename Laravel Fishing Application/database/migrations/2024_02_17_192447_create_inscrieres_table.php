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
        Schema::create('inscrieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('pescar_id');
            $table->foreignId('concurs_id');
            $table->foreignId('mansa_id');
            $table->foreignId('stand_id');
            $table->foreignId('lac_id');
            $table->foreignId('sector_id');
            $table->string('puncte_penalizare');
            $table->string('nume_trofeu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscrieres');
    }
};
