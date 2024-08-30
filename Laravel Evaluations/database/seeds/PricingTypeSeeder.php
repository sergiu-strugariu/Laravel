<?php

use App\Models\PricingType;
use Illuminate\Database\Seeder;

class PricingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PricingType::insert([
            ['id' => PricingType::WRITING_NATIVE, 'name' => "Writing Native"],
            ['id' => PricingType::WRITING, 'name' => "Writing Non-Native"],
            ['id' => PricingType::SPEAKING_NATIVE, 'name' => "Speaking Native"],
            ['id' => PricingType::SPEAKING, 'name' => "Speaking Non-Native"],
            ['id' => PricingType::READING, 'name' => "Reading"],
            ['id' => PricingType::LANGUAGE_USE, 'name' => "Language Use"],
            ['id' => PricingType::LANGUAGE_USE_NEW, 'name' => "Language Use New"],
            ['id' => PricingType::LISTENING, 'name' => "Listening"],
            ['id' => PricingType::CUSTOM_PERIOD_SPEAKING, 'name' => "Custom Period Speaking"],
        ]);
    }
}
