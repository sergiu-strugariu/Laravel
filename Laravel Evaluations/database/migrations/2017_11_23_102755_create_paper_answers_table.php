<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaperAnswersTable extends Migration
{

    public function up()
    {
        Schema::create('paper_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id')->unsigned();
            $table->integer('answer_id')->unsigned()->nullable();
            $table->integer('task_id')->unsigned();
            $table->integer('paper_id')->unsigned();
            $table->text('user_answer')->nullable();
            $table->integer('time')->nullable();
            $table->text('choices')->nullable();
            $table->string('observations', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('paper_answers');
    }
}