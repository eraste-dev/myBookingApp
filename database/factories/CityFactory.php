<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => function () {
                return Country::all()->random()->id;
            },
            'name'       => fake()->city(),
            'latitude'   => fake()->latitude(),
            'longitude'  => fake()->longitude(),
        ];
    }
}
