<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id'     => function () {
                return Hotel::all()->random()->id;
            },
            'type'         => fake()->country(),
            'price'        => fake()->numerify('######'),
            'availability' => fake()->boolean(),
        ];
    }
}
