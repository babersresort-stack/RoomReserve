<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $guest = User::query()->firstOrCreate(
            ['email' => 'guest@roomreserve.test'],
            [
                'name' => 'RoomReserve Guest',
                'password' => bcrypt('password'),
                'role' => 'guest',
            ]
        );

        $rooms = Room::query()->orderBy('code')->get();

        if ($rooms->isEmpty()) {
            return;
        }

        $reviewTemplates = [
            ['rating' => 5, 'comments' => 'Super clean room and smooth check-in. Staff were very accommodating.'],
            ['rating' => 4, 'comments' => 'Comfortable stay with strong Wi-Fi and good air conditioning.'],
            ['rating' => 5, 'comments' => 'Great value for the nightly rate. Will book again.'],
            ['rating' => 4, 'comments' => 'Quiet at night and bed was comfortable. Easy booking process too.'],
            ['rating' => 5, 'comments' => 'Excellent for family staycations. Room was exactly as posted.'],
            ['rating' => 4, 'comments' => 'Nice room ambiance and clean private comfort room.'],
            ['rating' => 5, 'comments' => 'Fast confirmation and hassle-free arrival experience.'],
            ['rating' => 4, 'comments' => 'Reception was friendly and responsive to requests.'],
            ['rating' => 5, 'comments' => 'Perfect for a weekend rest. Very peaceful surroundings.'],
            ['rating' => 4, 'comments' => 'Everything worked well and the place felt secure.'],
            ['rating' => 5, 'comments' => 'Loved the setup, especially for a group booking.'],
            ['rating' => 4, 'comments' => 'Clean linens, tidy bathroom, and quick check-out.'],
        ];

        $startDate = CarbonImmutable::now()->subDays(45)->startOfDay();

        foreach ($reviewTemplates as $index => $template) {
            $room = $rooms[$index % $rooms->count()];
            $checkIn = $startDate->addDays($index * 2)->setTime(14, 0);
            $checkOut = $checkIn->addDay()->setTime(12, 0);
            $reference = sprintf('SEEDRVW-%04d', $index + 1);

            $booking = Booking::query()->updateOrCreate(
                ['reference' => $reference],
                [
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_at' => $checkIn,
                    'check_out_at' => $checkOut,
                    'guests' => min(2, max(1, (int) $room->capacity)),
                    'status' => 'completed',
                    'special_requests' => null,
                    'cancelled_at' => null,
                    'cancellation_reason' => null,
                ]
            );

            Feedback::query()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id' => $guest->id,
                    'room_id' => $room->id,
                    'rating' => $template['rating'],
                    'comments' => $template['comments'],
                    'is_public' => true,
                ]
            );
        }
    }
}
