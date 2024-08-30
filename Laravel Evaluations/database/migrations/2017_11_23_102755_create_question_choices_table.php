<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionChoicesTable extends Migration {

	public function up()
	{
		Schema::create('question_choices', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('question_id')->unsigned();
			$table->boolean('correct')->default(0);
            $table->string('answer', 255);
			$table->softDeletes();
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('question_choices');
	}
}