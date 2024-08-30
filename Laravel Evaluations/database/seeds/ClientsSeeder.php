<?php
use App\Models\Client;
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/13/2017
 * Time: 12:04 PM
 */
class ClientsSeeder extends Seeder
{
    public function run()
    {

        Client::create(['name' => 'Vodafone']);
        Client::create(['name' => 'Orange']);
    }
}