<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class HotelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hotel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'            => fake()->city(),
            'location'        => fake()->address(),
            'description'     => fake()->paragraphs(fake()->numerify("#"), true),
            'hotel_latitude'  => fake()->latitude(),
            'hotel_longitude' => fake()->longitude(),
            'city_id'         => function () {
                return City::all()->random()->id;
            },
        ];
    }
}
