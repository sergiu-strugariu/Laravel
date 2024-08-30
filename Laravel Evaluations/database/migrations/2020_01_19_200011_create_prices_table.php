<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('language_id')->unsigned()->index()->nullable();
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');

            $table->integer('pricing_type_id')->unsigned()->index()->nullable();
            $table->foreign('pricing_type_id')->references('id')->on('pricing_type')->onDelete('cascade');

            $table->integer('client_id')->unsigned()->index()->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->integer('project_id')->unsigned()->index()->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->integer('level');
            $table->string('price');

            $table->unique(['language_id', 'pricing_type_id', 'client_id', 'project_id', 'level'], "prices_li_pti_ci_pi_l");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
