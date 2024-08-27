<?php

namespace Database\Seeders;

use App\Models\Concurs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConcursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Concurs::factory()
        ->count(50)
        ->create();
    }
}
