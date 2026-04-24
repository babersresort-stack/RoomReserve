<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'booking_id' => Booking::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'comments' => fake()->sentence(),
            'is_public' => true,
        ];
    }
}
