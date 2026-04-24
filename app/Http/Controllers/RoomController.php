<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomSearchRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\RoomAvailabilityService;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function __construct(private readonly RoomAvailabilityService $roomAvailabilityService)
    {
    }

    public function index(RoomSearchRequest $request): View
    {
        $filters = $request->validated();
        $rooms = $this->roomAvailabilityService->search($filters);

        return view($request->routeIs('admin.rooms.*') ? 'admin.rooms.index' : 'rooms.index', [
            'rooms' => $rooms,
            'isAdmin' => $request->user()?->role === 'admin',
            'filters' => array_merge([
                'room_id' => null,
                'check_in_at' => null,
                'check_out_at' => null,
                'guests' => null,
            ], $filters),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.rooms.form', [
            'room' => new Room(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedRoom($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeRoomImage($request->file('image'));
        }

        $data['amenities'] = $this->normalizeAmenities($data['amenities'] ?? null);

        Room::create($data);

        return redirect()->route('admin.rooms.index')->with('status', 'Room created successfully.');
    }

    public function show(Request $request, Room $room): View
    {
        $room = Room::query()
            ->withAvg('feedback as average_rating', 'rating')
            ->with(['feedback.user', 'bookings' => function ($query): void {
                $query->latest('check_in_at')->limit(5);
            }])
            ->findOrFail($room->id);

        $reservedDates = $room->bookings()
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->where('check_out_at', '>=', now()->startOfDay())
            ->get(['check_in_at', 'check_out_at'])
            ->flatMap(function (Booking $booking): array {
                $period = CarbonPeriod::create(
                    $booking->check_in_at->copy()->startOfDay(),
                    $booking->check_out_at->copy()->subDay()->startOfDay()
                );

                $dates = [];

                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }

                return $dates;
            })
            ->unique()
            ->values();

        return view('rooms.show', [
            'room' => $room,
            'isAdmin' => $request->user()?->role === 'admin',
            'availability' => ! Room::query()->whereKey($room->id)->where('status', '!=', 'available')->exists() && ! Booking::hasConflict($room->id, now(), now()->addHour()),
            'reservedDates' => $reservedDates,
        ]);
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.form', [
            'room' => $room,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $this->validatedRoom($request, $room->id);

        if ($request->hasFile('image')) {
            if ($room->image_path) {
                $this->deleteRoomImage($room->image_path);
            }

            $data['image_path'] = $this->storeRoomImage($request->file('image'));
        }

        $data['amenities'] = $this->normalizeAmenities($data['amenities'] ?? null);

        $room->update($data);

        return redirect()->route('admin.rooms.index')->with('status', 'Room updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->image_path) {
            $this->deleteRoomImage($room->image_path);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('status', 'Room deleted successfully.');
    }

    public function availability(Room $room): array
    {
        $now = now();

        $reservedDates = $room->bookings()
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->where('check_out_at', '>=', $now->copy()->startOfDay())
            ->selectRaw('DATE(check_in_at) as reserved_date')
            ->distinct()
            ->pluck('reserved_date')
            ->map(fn ($date): string => (string) $date)
            ->values();

        return [
            'room_id' => $room->id,
            'status' => $room->status,
            'is_available_now' => $room->status === 'available' && ! Booking::hasConflict($room->id, $now, $now->copy()->addHour()),
            'reserved_dates' => $reservedDates,
        ];
    }

    private function validatedRoom(Request $request, ?int $ignoreRoomId = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'code')->ignore($ignoreRoomId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['available', 'maintenance', 'unavailable'])],
            'amenities' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function normalizeAmenities(?string $amenities): array
    {
        if (! $amenities) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $amenities) ?: [])));
    }

    private function storeRoomImage($image): string
    {
        $directory = public_path('uploads/rooms');
        File::ensureDirectoryExists($directory);

        $fileName = Str::uuid()->toString() . '.' . ($image->getClientOriginalExtension() ?: 'jpg');
        $image->move($directory, $fileName);

        return 'uploads/rooms/' . $fileName;
    }

    private function deleteRoomImage(string $imagePath): void
    {
        $fullPath = public_path($imagePath);

        if (is_file($fullPath)) {
            File::delete($fullPath);
        }
    }

}
