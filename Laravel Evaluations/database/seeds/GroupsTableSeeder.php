<?php

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Group;

class GroupsTableSeeder extends Seeder
{

    public function run()
    {
        $languages = Language::all();
        foreach ($languages as $language) {
            Group::create(['language_id' => $language->id]);
        }
    }
}