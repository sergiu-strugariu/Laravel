<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/27/2017
 * Time: 5:20 PM
 */

use App\Models\ProjectTypes;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    public function run()
    {
        ProjectTypes::create(['name' => 'Linguistic Audit']);
        ProjectTypes::create(['name' => 'Courses Initial Tests']);
        ProjectTypes::create(['name' => 'Eucom Recruiting']);
    }
}