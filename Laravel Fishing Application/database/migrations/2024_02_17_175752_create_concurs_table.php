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
        Schema::create('concurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by');
            $table->string('nume');
            $table->foreignId('organizator_id');
            $table->text('descriere');
            $table->text('regulament');
            $table->text('poza');
            $table->text('start');
            $table->text('stop');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concurs');
    }
};
