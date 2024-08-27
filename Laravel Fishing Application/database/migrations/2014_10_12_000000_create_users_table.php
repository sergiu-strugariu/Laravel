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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('prenume');
            $table->string('nume');
            $table->string('tip')->default('Pescar');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('data_nasterii');
            $table->string('sex');
            $table->string('google_id')->nullable();
            $table->string('istoric_asociere')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
