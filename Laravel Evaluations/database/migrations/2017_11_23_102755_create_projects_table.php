<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsTable extends Migration {

	public function up()
	{
		Schema::create('projects', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('user_id')->unsigned();
            $table->integer('client_id')->unsigned();
			$table->integer('project_type_id')->unsigned();
			$table->boolean('default_bill_client')->default(0);
			$table->boolean('default_pay_assessor')->default(0);
            $table->softDeletes();
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('projects');
	}
}