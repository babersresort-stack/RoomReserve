<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('RM-###')),
            'name' => fake()->words(3, true) . ' Room',
            'description' => fake()->paragraph(),
            'capacity' => fake()->numberBetween(1, 6),
            'base_rate' => fake()->randomFloat(2, 80, 350),
            'status' => 'available',
            'image_path' => null,
            'amenities' => ['Wi-Fi', 'Air conditioning', 'Private bathroom'],
        ];
    }
}
