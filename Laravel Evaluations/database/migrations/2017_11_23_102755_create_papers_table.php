<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePapersTable extends Migration
{

    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('paper_type_id')->unsigned();
            $table->integer('task_id')->unsigned();
            $table->integer('status_id')->unsigned()->default(1);
            $table->integer('current_question_id')->unsigned()->nullable();
            $table->text('current_choices')->nullable();
            $table->float('current_audio_time')->nullable();
            $table->integer('question_current_time')->nullable();
            $table->boolean('done')->default(0);
            $table->boolean('reminder_update_sent')->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('papers');
    }
}