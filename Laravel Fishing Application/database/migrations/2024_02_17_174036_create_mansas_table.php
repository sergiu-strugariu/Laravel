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
        Schema::create('mansas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by');
            $table->foreignId('concurs_id');
            $table->foreignId('lac_id');
            $table->string('nume');
            $table->string('start_mansa');
            $table->string('stop_mansa');
            $table->string('status_mansa');
            $table->integer('participanti')->default(0);
            $table->integer('participanti_max');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mansas');
    }
};
