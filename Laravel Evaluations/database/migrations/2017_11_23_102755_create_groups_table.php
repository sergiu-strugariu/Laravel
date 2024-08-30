<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTable extends Migration {

	public function up()
	{
		Schema::create('groups', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('language_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('groups');
	}
}