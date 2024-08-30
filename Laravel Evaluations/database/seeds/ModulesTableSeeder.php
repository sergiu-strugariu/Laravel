<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/24/2017
 * Time: 1:29 PM
 */

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModulesTableSeeder extends Seeder
{

    public function run()
    {
        Module::create(['name' => 'Documentation', 'slug' => 'documentation']);
        Module::create(['name' => 'Sales', 'slug' => 'sales']);
        Module::create(['name' => 'Recruiting', 'slug' => 'recruiting']);
        Module::create(['name' => 'Intern Training', 'slug' => 'intern_training']);
        Module::create(['name' => 'Linguistic Audit', 'slug' => 'linguistic_audit']);
        Module::create(['name' => 'Courses', 'slug' => 'courses']);
        Module::create(['name' => 'Other Services', 'slug' => 'other_services']);
        Module::create(['name' => 'Accounting', 'slug' => 'accounting']);
        Module::create(['name' => 'Calendar Master', 'slug' => 'calendar_master']);
    }

}