<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewUpdatesToAssessors extends Migration
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
                    'id' => 16,
                    'name' => 'Candidate was called and said he/she would call back later',
                    'slug' => 'candidate-cancelled-call-back',
                    'display_name' => 'Candidate was called and said he/she would call back later'
                ),
                array(
                    'id' => 17,
                    'name' => 'Candidate did not pass the Identity validation step',
                    'slug' => 'candidate-client-not-validated',
                    'display_name' => 'Candidate did not pass the Identity validation step'
                ),
                array(
                    'id' => 18,
                    'name' => 'Candidate said he had issues during online test, asked to take it again',
                    'slug' => 'candidate-issues-during-test',
                    'display_name' => 'Candidate said he had issues during online test, asked to take it again'
                ),
                array(
                    'id' => 19,
                    'name' => 'Candidate was called several times but line was engaged each time. SMS text sent.',
                    'slug' => 'candidate-busy-sms-sent',
                    'display_name' => 'Candidate was called several times but line was engaged each time. SMS text sent.'
                ),
            )
        );

        DB::table('role_task_update')->insert(
            array(
                array(
                    'role_id' => 1,
                    'task_update_id' => 16,
                ),
                array(
                    'role_id' => 1,
                    'task_update_id' => 17,
                ),
                array(
                    'role_id' => 1,
                    'task_update_id' => 18,
                ),
                array(
                    'role_id' => 1,
                    'task_update_id' => 19,
                ),
                array(
                    'role_id' => 2,
                    'task_update_id' => 16,
                ),
                array(
                    'role_id' => 2,
                    'task_update_id' => 17,
                ),
                array(
                    'role_id' => 2,
                    'task_update_id' => 18,
                ),
                array(
                    'role_id' => 2,
                    'task_update_id' => 19,
                ),
                array(
                    'role_id' => 7,
                    'task_update_id' => 16,
                ),
                array(
                    'role_id' => 7,
                    'task_update_id' => 17,
                ),
                array(
                    'role_id' => 7,
                    'task_update_id' => 18,
                ),
                array(
                    'role_id' => 7,
                    'task_update_id' => 19,
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
        DB::table('task_updates')->delete('16');
        DB::table('task_updates')->delete('17');
        DB::table('task_updates')->delete('18');
        DB::table('task_updates')->delete('19');

        DB::table("role_task_update")
            ->where(
                array(
                    "task_update_id" => 16
                )
            )
            ->orWhere(
                array(
                    "task_update_id" => 17
                )
            )
            ->orWhere(
                array(
                    "task_update_id" => 18
                )
            )
            ->orWhere(
                array(
                    "task_update_id" => 19
                )
            )
            ->delete();
    }
}
