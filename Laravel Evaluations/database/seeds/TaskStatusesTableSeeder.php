<?php

use Illuminate\Database\Seeder;
use App\Models\TaskStatus;

class TaskStatusesTableSeeder extends Seeder
{

    public function run()
    {
        TaskStatus::create(['name' => 'Allocated', 'color' => '#d3d3d3']);
        TaskStatus::create(['name' => 'In Progress', 'color' => '#F1C40F']);
        TaskStatus::create(['name' => 'Done', 'color' => '#27AE60']);
        TaskStatus::create(['name' => 'Issue', 'color' => '#EBEBEB']);
        TaskStatus::create(['name' => 'Canceled', 'color' => '#E74C3C']);
    }
}