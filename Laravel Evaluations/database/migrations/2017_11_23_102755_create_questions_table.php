<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionsTable extends Migration {

	public function up()
	{
		Schema::create('questions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('language_paper_type_id')->unsigned();
			$table->integer('question_level_id')->unsigned()->nullable();
			$table->integer('q_type')->nullable();
			$table->integer('v_number')->nullable();
			$table->string('code', 12)->nullable();
			$table->tinyInteger('language_use_type')->nullable();
            $table->text('body')->nullable();
            $table->string('audio_file_path', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->integer('max_words')->nullable();
            $table->integer('time')->nullable();
            $table->softDeletes();
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('questions');
	}
}