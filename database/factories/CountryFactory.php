<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => fake()->country(),
            'iso_code'  => fake()->unique()->countryCode(),
            'latitude'  => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
}
