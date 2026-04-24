<?php

namespace App\Services;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RoomAvailabilityService
{
    public function search(array $filters): Collection
    {
        $query = Room::query()
            ->withCount(['bookings as active_booking_count' => function ($bookingQuery): void {
                $bookingQuery->active();
            }])
            ->withAvg('feedback as average_rating', 'rating')
            ->orderBy('code');

        if (! empty($filters['room_id'])) {
            $query->whereKey((int) $filters['room_id']);
        }

        if (! empty($filters['guests'])) {
            $query->where('capacity', '>=', (int) $filters['guests']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $query->where(function (Builder $searchQuery) use ($searchTerm): void {
                $searchQuery->where('code', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $checkIn = $this->safeParseDate($filters['check_in_at'] ?? null);
        $checkOut = $this->safeParseDate($filters['check_out_at'] ?? null);

        if ($checkIn && $checkOut && $checkOut->greaterThan($checkIn)) {
            $query->where('status', 'available')
                ->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($checkIn, $checkOut): void {
                    $bookingQuery
                        ->where('status', '!=', 'cancelled')
                        ->where('check_in_at', '<', $checkOut)
                        ->where('check_out_at', '>', $checkIn);
                });
        }

        return $query->get();
    }

    private function safeParseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
