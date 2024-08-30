<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTaskUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("role_task_update")
            ->where(
                array(
                    "task_update_id" => 14
                )
            )
            ->where(
                array(
                    "role_id" => 5
                )
            )
            ->delete();

        DB::table("role_task_update")
            ->where(
                array(
                    "task_update_id" => 15
                )
            )
            ->where(
                array(
                    "role_id" => 5
                )
            )
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
