<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserGroupsTable extends Migration {

	public function up()
	{
		Schema::create('user_groups', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('group_id')->unsigned();
            $table->boolean('native')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('user_groups');
	}
}