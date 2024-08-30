<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskStatusesTable extends Migration {

	public function up()
	{
		Schema::create('task_statuses', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('color');
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('task_statuses');
	}
}