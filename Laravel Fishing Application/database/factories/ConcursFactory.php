<?php

namespace Database\Factories;

use App\Models\Concurs;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concurs>
 */
class ConcursFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */

    protected $model = Concurs::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "created_by" => 1,
            "nume" => "Concurs " . fake()->name(),
            "organizator_id" => 1,
            "descriere" => fake()->text(),
            "regulament" => fake()->text(),
            "poza" => '5992037.jpg',
            "start" => Date::now(),
            "stop" => Date::now(),
        ];
    }
}
