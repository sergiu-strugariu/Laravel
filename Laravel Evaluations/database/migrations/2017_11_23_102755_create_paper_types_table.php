<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaperTypesTable extends Migration {

	public function up()
	{
		Schema::create('paper_types', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
	}

	public function down()
	{
		Schema::drop('paper_types');
	}
}