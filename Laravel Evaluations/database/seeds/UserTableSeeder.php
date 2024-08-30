<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/23/2017
 * Time: 4:28 PM
 */

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        User::create(['email' => 'master@mail.com', 'password' => bcrypt('test1234')]);
        User::create(['email' => 'admin@mail.com', 'password' => bcrypt('test1234')]);
        User::create(['email' => 'clientFirst@mail.com', 'password' => bcrypt('test1234'), 'client_id' => 1, 'first_name' => 'ClientFirst', 'last_name' => 'ClientFirst']);
        User::create(['email' => 'clientSecond@mail.com', 'password' => bcrypt('test1234'), 'client_id' => 2, 'first_name' => 'ClientSecond', 'last_name' => 'ClientSecond']);
        User::create(['email' => 'recruiter@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Recruiter', 'last_name' => 'Recruiter']);
        User::create(['email' => 'css@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Css', 'last_name' => 'Css']);
        User::create(['email' => 'tds@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Tds', 'last_name' => 'Tds', 'client_id' => 1]);
        User::create(['email' => 'recruiter2@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Recruiter2', 'last_name' => 'Recruiter2']);
        User::create(['email' => 'css2@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Css2', 'last_name' => 'Css2']);
        User::create(['email' => 'tds2@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Tds2', 'last_name' => 'Tds2', 'client_id' => 2]);
        User::create(['email' => 'assessor1@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Assessor1', 'last_name' => 'Assessor1']);
        User::create(['email' => 'assessor2@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Assessor2', 'last_name' => 'Assessor2']);
        User::create(['email' => 'assessor3@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Assessor3', 'last_name' => 'Assessor3']);
        User::create(['email' => 'assessor4@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Assessor4', 'last_name' => 'Assessor4']);
        User::create(['email' => 'assessor5@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Assessor5', 'last_name' => 'Assessor5']);
        User::create(['email' => 'assessor6@mail.com', 'password' => bcrypt('test1234'), 'first_name' => 'Assessor6', 'last_name' => 'Assessor6']);
    }
}