<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/23/2017
 * Time: 4:09 PM
 */

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{

    public function run()
    {
        Role::create(['name' => 'Master', 'slug' => 'master']);
        Role::create(['name' => 'Administrator Modul Audit Lingvistic', 'slug' => 'administrator']);
        Role::create(['name' => 'Recruiter', 'slug' => 'recruiter']);
        Role::create(['name' => 'Css', 'slug' => 'css']);
        Role::create(['name' => 'Client', 'slug' => 'client']);
        Role::create(['name' => 'TDS', 'slug' => 'tds']);
        Role::create(['name' => 'Assessor', 'slug' => 'assessor']);
    }
}