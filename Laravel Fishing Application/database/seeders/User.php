<?php

namespace Database\Seeders;

use App\Models\User as ModelsUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newUser = ModelsUser::create([
            'prenume'     => 'Sergiu',
            'nume'     => 'Strugariu',
            'tip'     => 'Pescar',
            'mobile'     => 'mobile1',
            'data_nasterii'     => 'data',
            'sex'     => '1',
            'google_id'     => null,
            'email'    => 'sstrugariu.7@icloud.com',
            'password' => 'Sergiu1234'
        ]);
    }
}
