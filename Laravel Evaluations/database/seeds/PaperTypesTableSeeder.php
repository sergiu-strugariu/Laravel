<?php

use Illuminate\Database\Seeder;
use App\Models\PaperType;

class PaperTypesTableSeeder extends Seeder
{

    public function run()
    {
        PaperType::create(['name' => 'Language Use New']);
        PaperType::create(['name' => 'Speaking']);
        PaperType::create(['name' => 'Writing']);
        PaperType::create(['name' => 'Listening']);
        PaperType::create(['name' => 'Reading']);
        PaperType::create(['name' => 'Language Use']);
    }
}