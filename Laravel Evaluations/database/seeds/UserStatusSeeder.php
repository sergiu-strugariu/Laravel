<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/28/2017
 * Time: 5:57 PM
 */

use Illuminate\Database\Seeder;
use App\Models\UserStatus;

class UserStatusSeeder extends Seeder
{
    public function run()
    {
        UserStatus::create(['name' => 'Active', 'color' => 'green']);
        UserStatus::create(['name' => 'StandBy', 'color' => 'yellow']);
    }
}