<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration
{

    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('assessor_id')->unsigned()->nullable();
            $table->integer('language_id')->unsigned()->nullable();
            $table->integer('task_status_id')->unsigned()->default(1);
            $table->integer('added_by_id')->unsigned();
            $table->string('name');
            $table->string('skype')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->timestamp('deadline')->nullable();
            $table->timestamp('availability_from')->nullable();
            $table->timestamp('availability_to')->nullable();
            $table->string('mark')->nullable();
            $table->string('department')->nullable();
            $table->boolean('bill_client')->default(0);
            $table->boolean('pay_assessor')->default(1);
            $table->string('link', 255)->nullable();
            $table->timestamp('link_expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('tasks');
    }
}