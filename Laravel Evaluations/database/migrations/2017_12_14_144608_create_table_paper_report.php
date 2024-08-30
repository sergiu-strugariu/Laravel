<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaperReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('paper_id')->unsigned();
            $table->integer('assessor_id')->unsigned()->nullable();
            $table->float('ability')->unsigned();
            $table->string('grade', 50);
            $table->text('assessments')->nullable();
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
        Schema::table('paper_report', function (Blueprint $table) {
            Schema::drop('paper_report');
        });
    }
}
