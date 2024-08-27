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
        Schema::create('palmares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->nullable();
            $table->string('luna');
            $table->string('an');
            $table->string('organizator');
            $table->string('pescar');
            $table->string('data_concurs');
            $table->string('lac');
            $table->string('nume_concurs');
            $table->string('mansa');
            $table->string('stand');
            $table->string('cantitate');
            $table->string('puncte');
            $table->string('loc_sector');
            $table->string('loc_general');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palmares');
    }
};
