<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 day', '+14 days');
        $checkOut = (clone $checkIn)->modify('+2 days');

        return [
            'reference' => strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'guests' => fake()->numberBetween(1, 4),
            'status' => 'confirmed',
            'special_requests' => fake()->sentence(),
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }
}
