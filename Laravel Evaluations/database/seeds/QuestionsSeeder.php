<?php

use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\LanguagePaperTypes;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectParticipant;

use Faker\Factory;

/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/3/2018
 * Time: 4:09 PM
 */
class QuestionsSeeder extends Seeder
{
    public function run()
    {

        $faker = Factory::create();

        Project::create([
            'name' => 'Project One',
            'user_id' => 1,
            'client_id' => 1,
            'project_type_id' => 1,
            'default_bill_client' => 0,
            'default_pay_assessor' => 0,
        ]);

        ProjectParticipant::create([
            'project_id' => 1,
            'user_id' => 3,
        ]);

        LanguagePaperTypes::create(['language_id' => 1, 'paper_type_id' => 1]); // language use 1 new
        LanguagePaperTypes::create(['language_id' => 1, 'paper_type_id' => 3]); // writing 2
        LanguagePaperTypes::create(['language_id' => 1, 'paper_type_id' => 4]); // listening 3
        LanguagePaperTypes::create(['language_id' => 1, 'paper_type_id' => 5]); // reading 4
        LanguagePaperTypes::create(['language_id' => 1, 'paper_type_id' => 2]); // speaking 5
        LanguagePaperTypes::create(['language_id' => 1, 'paper_type_id' => 6]); // language use 6

        $questionsNr = 60;
        $choicesNr = 8;
        $qTypesNr = 19;
        $last_id = 0;

//        // writing
        for ($i = 1; $i <= $questionsNr; $i++) {
            Question::create([
                'language_paper_type_id' => 2,
                'body' => $faker->text(),
                'max_words' => random_int(5, 12),
                'time' => random_int(10, 35)
            ]);
            $last_id++;
        }

        //reading
        $level = 0;

        for ($i = 1; $i <= $questionsNr; $i++) {



            if ($level == 6) {
                $level = 1;
            } else {
                $level++;
            }

            for( $q = 1; $q <= $qTypesNr; $q++ ){

                Question::create([
                    'language_paper_type_id' => 4,
                    'question_level_id' => $level,
                    'description' => 'testing '.$faker->words(2, true),
                    'q_type' => $q,
                    'body' => $faker->text() . ' - diff .' . $level,
                    'max_words' => random_int(5, 12),
                    'time' => random_int(10, 35)
                ]);
                for ($j = 1; $j <= $choicesNr; $j++) {
                    QuestionChoice::create([
                        'question_id' => 1 + $last_id,
                        'correct' => ($j == 1 ? 1 : 0),
                        'answer' => $j == 1 ? '** correct' : $faker->words(1, true)
                    ]);
                }

                $last_id++;
            }


        }


        //lang use
        //reading
        $level = 0;
        for ($i = 1; $i <= $questionsNr; $i++) {

            if ($level == 6) {
                $level = 1;
            } else {
                $level++;
            }

            for( $q = 1; $q <= $qTypesNr; $q++ ){

                Question::create([
                    'language_paper_type_id' => 1,
                    'question_level_id' => $level,
                    'description' => 'testing '.$faker->words(2, true),
                    'q_type' => $q,
                    'language_use_type' => 1,
                    'body' => 'LU - ' . $faker->text() . ' - diff .' . $level,
                    'max_words' => random_int(5, 12),
                    'time' => random_int(10, 35)
                ]);
                for ($j = 1; $j <= $choicesNr; $j++) {
                    QuestionChoice::create([
                        'question_id' => 1 + $last_id,
                        'correct' => ($j == 1 ? 1 : 0),
                        'answer' => $j == 1 ? '** correct' : $faker->words(1, true)
                    ]);
                }
                $last_id++;
            }
        }

        //lang use
        //arrange
        $level = 0;
        for ($i = 1; $i <= $questionsNr; $i++) {

            if ($level == 6) {
                $level = 1;
            } else {
                $level++;
            }

            for( $q = 1; $q <= $qTypesNr; $q++ ) {

                Question::create([
                    'language_paper_type_id' => 1,
                    'question_level_id' => $level,
                    'language_use_type' => 2,
                    'description' => 'testing '.$faker->words(2, true),
                    'q_type' => $q,
                    'body' => json_encode($faker->words(rand(4, 6))),
                    'max_words' => random_int(5, 12),
                    'time' => random_int(10, 35)
                ]);

                $last_id++;

            }
        }


        //lang use
        //gap
        $level = 0;
        for ($i = 1; $i <= $questionsNr; $i++) {

            if ($level == 6) {
                $level = 1;
            } else {
                $level++;
            }

            for( $q = 1; $q <= $qTypesNr; $q++ ) {

                Question::create([
                    'language_paper_type_id' => 1,
                    'question_level_id' => $level,
                    'language_use_type' => 3,
                    'description' => 'testing '.$faker->words(2, true),
                    'q_type' => $q,
                    'body' => $faker->words(rand(1, 3), true) . ' ___ ' . $faker->words(rand(1, 2), true),
                    'max_words' => random_int(5, 12),
                    'time' => random_int(10, 35)
                ]);

                QuestionChoice::create([
                    'question_id' => 1 + $last_id,
                    'correct' => 1,
                    'answer' => 'correct'
                ]);

                $last_id++;

            }

        }


        //listening
        $level = 0;
        for ($i = 1; $i <= $questionsNr; $i++) {

            if ($level == 6) {
                $level = 1;
            } else {
                $level++;
            }


            for( $q = 1; $q <= $qTypesNr; $q++ ) {

                Question::create([
                    'language_paper_type_id' => 3,
                    'question_level_id' => $level,
                    'q_type' => $q,
                    'description' => 'testing '.$faker->words(2, true),
                    'body' => $faker->text() . ' - diff .' . $level,
                    'max_words' => random_int(5, 12),
                    'time' => random_int(10, 35),
                ]);
                for ($j = 1; $j <= $choicesNr; $j++) {
                    QuestionChoice::create([
                        'question_id' => 1 + $last_id,
                        'correct' => ($j == 1 ? 1 : 0),
                        'answer' => $j == 1 ? '** correct' : $faker->words(1, true)
                    ]);
                }

                $last_id++;

            }
        }

        //language use 60

        for ($i = 1; $i <= $questionsNr; $i++) {

            Question::create([
                'language_paper_type_id' => 6,
                'description' => 'testing '.$faker->words(2, true),
                'body' => $faker->text() . ' - lu('.$i.')'
            ]);
            for ($j = 1; $j <= $choicesNr; $j++) {
                QuestionChoice::create([
                    'question_id' => 1 + $last_id,
                    'correct' => ($j == 1 ? 1 : 0),
                    'answer' => $j == 1 ? '** correct' : $faker->words(1, true)
                ]);
            }

            $last_id++;


        }


        Question::withTrashed()->where('id', '>', 0)->restore();

    }
}