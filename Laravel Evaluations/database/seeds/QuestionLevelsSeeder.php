<?php

use App\Models\QuestionLevel;
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/3/2018
 * Time: 4:09 PM
 */
class QuestionLevelsSeeder extends Seeder
{
    public function run()
    {
        QuestionLevel::create(['name' => 'A1', 'difficulty' => 1]);
        QuestionLevel::create(['name' => 'A2', 'difficulty' => 2]);
        QuestionLevel::create(['name' => 'B1', 'difficulty' => 3]);
        QuestionLevel::create(['name' => 'B2', 'difficulty' => 4]);
        QuestionLevel::create(['name' => 'C1', 'difficulty' => 5]);
        QuestionLevel::create(['name' => 'C2', 'difficulty' => 6]);
    }
}