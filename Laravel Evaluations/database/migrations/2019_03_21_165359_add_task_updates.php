<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('task_updates')->insert(array(
                array(
                    'id' => 14,
                    'name' => 'Please contact candidate via Whatsapp',
                    'slug' => 'contact-via-whatsapp',
                    'display_name' => 'Please contact candidate via Whatsapp'
                ),
                array(
                    'id' => 15,
                    'name' => 'Please contact candidate via Skype',
                    'slug' => 'contact-via-skype',
                    'display_name' => 'Please contact candidate via Skype'
                ),
            )
        );

        DB::table('role_task_update')->insert(
            array(
                array(
                    'role_id' => 1,
                    'task_update_id' => 14,
                ),
                array(
                    'role_id' => 1,
                    'task_update_id' => 15,
                ),
                array(
                    'role_id' => 2,
                    'task_update_id' => 14,
                ),
                array(
                    'role_id' => 2,
                    'task_update_id' => 15,
                ),
                array(
                    'role_id' => 5,
                    'task_update_id' => 14,
                ),
                array(
                    'role_id' => 5,
                    'task_update_id' => 15,
                ),
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('task_updates')->delete('14');
        DB::table('task_updates')->delete('15');

        DB::table("role_task_update")
            ->where(
                array(
                    "task_update_id" => 14
                )
            )
            ->orWhere(
                array(
                    "task_update_id" => 15
                )
            )
            ->delete();
    }
}
