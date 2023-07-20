<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'        => function () {
                return User::all()->random()->id;
            },
            'room_id'        => function () {
                return Room::all()->random()->id;
            },
            'check_in_date'  => fake()->date('Y-m-d H:i:s'),
            'check_out_date' => fake()->date('Y-m-d H:i:s'),
        ];
    }
}
