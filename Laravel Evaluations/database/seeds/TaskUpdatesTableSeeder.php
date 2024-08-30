<?php

use Illuminate\Database\Seeder;

class TaskUpdatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('task_updates')->delete();
        
        \DB::table('task_updates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'slug' => 'reschedule',
                'name' => 'Reschedule',
                'display_name' => 'Reschedule',
            ),
            1 => 
            array (
                'id' => 2,
                'slug' => 'status-update',
                'name' => 'Request status update',
                'display_name' => 'Client requested status update',
            ),
            2 => 
            array (
                'id' => 3,
                'slug' => 'no-answer',
                'name' => 'No answer. SMS text sent.',
                'display_name' => 'No answer. SMS text sent.',
            ),
            3 => 
            array (
                'id' => 4,
                'slug' => 'phone-off',
                'name' => 'Phone off. SMS text sent.',
                'display_name' => 'Phone off. SMS text sent.',
            ),
            4 => 
            array (
                'id' => 5,
                'slug' => 'candidate-no-longer',
                'name' => 'Candidate no longer interested.',
                'display_name' => 'Candidate no longer interested.',
            ),
            5 => 
            array (
                'id' => 6,
                'slug' => 'candidate-refused',
                'name' => 'Candidate refused to be assessed.',
                'display_name' => 'Candidate refused to be assessed.',
            ),
            6 => 
            array (
                'id' => 7,
                'slug' => 'candidate-different-language',
                'name' => 'Candidate indicated that he/she should be assessed for a different language.',
                'display_name' => 'Candidate indicated that he/she should be assessed for a different language.',
            ),
            7 => 
            array (
                'id' => 8,
                'slug' => 'wrong-number',
                'name' => 'Wrong number. Someone else answered.',
                'display_name' => 'Wrong number. Someone else answered.',
            ),
            8 => 
            array (
                'id' => 9,
                'slug' => 'client-request-phone',
                'name' => 'Ask client to check phone number.',
                'display_name' => 'Assessor requested phone number check.',
            ),
            9 => 
            array (
                'id' => 10,
                'slug' => 'client-request-skype',
                'name' => 'Request candidate Skype ID.',
                'display_name' => 'Assessor requested candidate Skype ID.',
            ),
            10 => 
            array (
                'id' => 11,
                'slug' => 'bad-reception',
                'name' => 'Bad reception. The candidate will call back later.',
                'display_name' => 'Bad reception. The candidate will call back later.',
            ),
            11 => 
            array (
                'id' => 12,
                'slug' => 'candidate-did-not-answer',
                'name' => 'Candidate did not answer at scheduled time.',
                'display_name' => 'Candidate did not answer at scheduled time.',
            ),
        ));
        
        
    }
}