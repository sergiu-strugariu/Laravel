<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/11/2017
 * Time: 8:44 AM
 */

use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        UserRole::create(['role_id' => 1, 'user_id' => 1]);
        UserRole::create(['role_id' => 2, 'user_id' => 2]);
        UserRole::create(['role_id' => 5, 'user_id' => 3]);
        UserRole::create(['role_id' => 5, 'user_id' => 4]);
        UserRole::create(['role_id' => 3, 'user_id' => 5]);
        UserRole::create(['role_id' => 4, 'user_id' => 6]);
        UserRole::create(['role_id' => 6, 'user_id' => 7]);
        UserRole::create(['role_id' => 3, 'user_id' => 8]);
        UserRole::create(['role_id' => 4, 'user_id' => 9]);
        UserRole::create(['role_id' => 6, 'user_id' => 10]);
        UserRole::create(['role_id' => 7, 'user_id' => 11]);
        UserRole::create(['role_id' => 7, 'user_id' => 12]);
        UserRole::create(['role_id' => 7, 'user_id' => 13]);
        UserRole::create(['role_id' => 7, 'user_id' => 14]);
        UserRole::create(['role_id' => 7, 'user_id' => 15]);
        UserRole::create(['role_id' => 7, 'user_id' => 16]);
    }
}

