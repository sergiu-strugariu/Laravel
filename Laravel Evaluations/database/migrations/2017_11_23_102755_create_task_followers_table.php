<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskFollowersTable extends Migration {

	public function up()
	{
		Schema::create('task_followers', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('task_id')->unsigned();
			$table->integer('user_id')->unsigned();
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('task_followers');
	}
}