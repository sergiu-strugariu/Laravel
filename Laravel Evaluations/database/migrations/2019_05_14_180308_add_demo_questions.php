<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDemoQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('questions')->insert(
            array(
                array(
                    'id' => 99991,
                    'language_paper_type_id' => '1',
                    'question_level_id' => '2',
                    'q_type' => '1',
                    'v_number' => '2',
                    'code' => 'A2 Q1 V2',
                    'language_use_type' => '2',
                    'body' => '["a","b","c","d","e","f","g"]',
                    'audio_file_path' => NULL,
                    'title' => "Demo arrange word question",
                    'description' => 'Demo arrange word question',
                    'max_words' => NULL,
                    'time' => '99991',
                    'deleted_at' => '2019-05-14 17:59:26',
                    'created_at' => '2019-05-10 11:08:16',
                    'updated_at' => '2019-05-10 11:44:15'
                ),
                array(
                    'id' => 99992,
                    'language_paper_type_id' => '1',
                    'question_level_id' => '2',
                    'q_type' => '1',
                    'v_number' => '2',
                    'code' => 'A2 Q1 V2',
                    'language_use_type' => '1',
                    'body' => 'Demo select correct choice question',
                    'audio_file_path' => NULL,
                    'title' => "Demo select correct choice question",
                    'description' => 'Demo select correct choice question',
                    'max_words' => NULL,
                    'time' => '99992',
                    'deleted_at' => '2019-05-14 17:59:26',
                    'created_at' => '2019-05-10 11:08:16',
                    'updated_at' => '2019-05-10 11:44:15'
                ),
                array(
                    'id' => 99993,
                    'language_paper_type_id' => '1',
                    'question_level_id' => '2',
                    'q_type' => '1',
                    'v_number' => '2',
                    'code' => 'A2 Q1 V2',
                    'language_use_type' => '3',
                    'body' => 'Complete the _ question',
                    'audio_file_path' => NULL,
                    'title' => "Demo complete the sentence question",
                    'description' => 'Demo complete the sentence question',
                    'max_words' => NULL,
                    'time' => '99993',
                    'deleted_at' => '2019-05-14 17:59:26',
                    'created_at' => '2019-05-10 11:08:16',
                    'updated_at' => '2019-05-10 11:44:15'
                ),
            )
        );


        DB::table('question_choices')->insert(
            array(
                array('id' => '999921','question_id' => '99992','correct' => '1','answer' => 'This the first choice','deleted_at' => NULL,'created_at' => '2019-05-09 15:06:16','updated_at' => '2019-05-09 15:06:16'),
                array('id' => '999922','question_id' => '99992','correct' => '0','answer' => 'This the second choice','deleted_at' => NULL,'created_at' => '2019-05-09 15:06:16','updated_at' => '2019-05-09 15:06:16'),
                array('id' => '999923','question_id' => '99992','correct' => '0','answer' => 'This the third choice','deleted_at' => NULL,'created_at' => '2019-05-09 15:06:16','updated_at' => '2019-05-09 15:06:16'),
                array('id' => '999924','question_id' => '99992','correct' => '0','answer' => 'This the fourth choice','deleted_at' => NULL,'created_at' => '2019-05-09 15:06:16','updated_at' => '2019-05-09 15:06:16'),

                array('id' => '999931','question_id' => '99993','correct' => '1','answer' => 'correct','deleted_at' => NULL,'created_at' => '2019-05-14 17:59:26','updated_at' => '2019-05-14 17:59:26')
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
        DB::table('questions')
            ->whereIn('id', [99991, 99992, 99993])
            ->delete();

        DB::table('question_choices')
            ->whereIn('id', [999921, 999922, 999923, 999924, 999931])
            ->delete();
    }
}
