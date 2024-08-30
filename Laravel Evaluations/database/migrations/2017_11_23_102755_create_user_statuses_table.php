<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserStatusesTable extends Migration {

	public function up()
	{
		Schema::create('user_statuses', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('color');
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('user_statuses');
	}
}