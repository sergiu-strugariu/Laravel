<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionLevelsTable extends Migration {

	public function up()
	{
		Schema::create('question_levels', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->smallInteger('difficulty');
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('question_levels');
	}
}