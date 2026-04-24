<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_booking_and_conflicts_are_blocked(): void
    {
        $guest = User::factory()->create(['role' => 'guest']);
        $room = Room::factory()->create(['status' => 'available', 'capacity' => 4]);

        $this->actingAs($guest)
            ->post(route('bookings.store', $room), [
                'check_in_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'check_out_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
                'guests' => 2,
                'special_requests' => 'Late check-in',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseHas('bookings', [
            'room_id' => $room->id,
            'user_id' => $guest->id,
            'status' => 'confirmed',
        ]);

        $this->actingAs($guest)
            ->post(route('bookings.store', $room), [
                'check_in_at' => now()->addDay()->addHours(2)->format('Y-m-d\TH:i'),
                'check_out_at' => now()->addDays(4)->format('Y-m-d\TH:i'),
                'guests' => 2,
                'special_requests' => 'Second stay',
            ])
            ->assertSessionHasErrors(['check_in_at']);

        $this->assertSame(1, Booking::count());
    }

    public function test_admin_can_access_management_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin dashboard');
    }
}
