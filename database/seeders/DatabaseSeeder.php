<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@roomreserve.test',
        ], [
            'name' => 'RoomReserve Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::query()->updateOrCreate([
            'email' => 'guest@roomreserve.test',
        ], [
            'name' => 'RoomReserve Guest',
            'password' => Hash::make('password'),
            'role' => 'guest',
        ]);

        $rooms = [
            [
                'code' => 'RM-101',
                'name' => 'Single Room 101',
                'capacity' => 2,
                'base_rate' => 2000,
                'status' => 'available',
                'description' => 'One-bed setup for up to two guests.',
                'image_path' => 'resources/asset/single.jpg',
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Hot shower'],
            ],
            [
                'code' => 'RM-102',
                'name' => 'Single Room 102',
                'capacity' => 2,
                'base_rate' => 2000,
                'status' => 'available',
                'description' => 'Same one-bed setup, alternate room option.',
                'image_path' => 'resources/asset/single2.jpg',
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Smart TV'],
            ],
            [
                'code' => 'RM-103',
                'name' => 'Double Room 103',
                'capacity' => 2,
                'base_rate' => 2200,
                'status' => 'available',
                'description' => 'Comfortable two-person room with private comfort room.',
                'image_path' => 'resources/asset/double.jpg',
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Private bathroom'],
            ],
            [
                'code' => 'RM-104',
                'name' => 'Double Room 104',
                'capacity' => 2,
                'base_rate' => 2200,
                'status' => 'available',
                'description' => 'Two-person option with the same room style as 103.',
                'image_path' => 'resources/asset/double2.jpg',
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Desk area'],
            ],
            [
                'code' => 'RM-105',
                'name' => 'Barkada Room 105',
                'capacity' => 10,
                'base_rate' => 1000,
                'status' => 'available',
                'description' => 'Shared group room. Pricing is 1,000 per head for overnight stay.',
                'image_path' => 'resources/asset/double3.jpg',
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Group sleeping area'],
            ],
        ];

        foreach ($rooms as $room) {
            Room::query()->updateOrCreate([
                'code' => $room['code'],
            ], $room);
        }

        $this->call(FeedbackSeeder::class);
    }
}
