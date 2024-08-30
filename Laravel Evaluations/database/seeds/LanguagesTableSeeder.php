<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/23/2017
 * Time: 4:09 PM
 */

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesTableSeeder extends Seeder
{

    public function run()
    {
        Language::create(['name' => 'English']);
        Language::create(['name' => 'German']);
        Language::create(['name' => 'Dutch']);
        Language::create(['name' => 'French']);
        Language::create(['name' => 'Italian']);
        Language::create(['name' => 'Russian']);
        Language::create(['name' => 'Turkish']);
        Language::create(['name' => 'Spanish']);
        Language::create(['name' => 'Romanian']);
        Language::create(['name' => 'Czech']);
        Language::create(['name' => 'Greek']);
        Language::create(['name' => 'Hungarian']);
    }
}