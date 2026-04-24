<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Room;
use App\Notifications\BookingConfirmationNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()->with(['room', 'user', 'feedback'])->orderByDesc('check_in_at');

        if ($request->user()?->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('date')) {
            $query->whereDate('check_in_at', $request->string('date')->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('availability')) {
            $availability = $request->string('availability')->toString();

            if ($availability === 'unavailable') {
                $query->whereIn('status', Booking::ACTIVE_STATUSES)
                    ->where('check_out_at', '>', now());
            }

            if ($availability === 'available') {
                $query->where(function ($builder): void {
                    $builder->whereNotIn('status', Booking::ACTIVE_STATUSES)
                        ->orWhere('check_out_at', '<=', now());
                });
            }
        }

        return view('bookings.index', [
            'bookings' => $query->get(),
            'isAdmin' => $request->user()?->role === 'admin',
            'filters' => [
                'date' => $request->string('date')->toString(),
                'status' => $request->string('status')->toString(),
                'availability' => $request->string('availability')->toString(),
            ],
        ]);
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeBooking($request, $booking);

        $booking->load(['room', 'user', 'feedback.user']);

        return view('bookings.show', [
            'booking' => $booking,
            'isAdmin' => $request->user()?->role === 'admin',
            'canReview' => $booking->status !== 'cancelled' && now()->greaterThanOrEqualTo($booking->check_out_at),
        ]);
    }

    public function store(Request $request, Room $room): RedirectResponse
    {
        $user = $request->user();
        $data = $this->validatedBooking($request, $room->capacity);
        $checkIn = Carbon::parse($data['check_in_at']);
        $checkOut = Carbon::parse($data['check_out_at']);

        if ($room->status !== 'available') {
            return back()->withErrors(['room' => 'This room is not currently available.'])->withInput();
        }

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return back()->withErrors(['check_out_at' => 'The check-out date must be after check-in.'])->withInput();
        }

        if (Booking::hasConflict($room->id, $checkIn, $checkOut)) {
            return back()->withErrors(['check_in_at' => 'This room is already booked for the selected dates.'])->withInput();
        }

        $booking = Booking::create([
            'reference' => 'RR-' . Str::upper(Str::random(8)),
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'guests' => $data['guests'],
            'status' => 'confirmed',
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        Notification::send($user, new BookingConfirmationNotification(
            $booking->load('room'),
            'confirmed',
            'Your room reservation has been confirmed.'
        ));

        return redirect()->route('bookings.show', $booking)->with('status', 'Reservation confirmed.');
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        $data = $this->validatedBooking($request, $booking->room->capacity);
        $checkIn = Carbon::parse($data['check_in_at']);
        $checkOut = Carbon::parse($data['check_out_at']);

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return back()->withErrors(['check_out_at' => 'The check-out date must be after check-in.'])->withInput();
        }

        if (Booking::hasConflict($booking->room_id, $checkIn, $checkOut, $booking->id)) {
            return back()->withErrors(['check_in_at' => 'This room is already booked for the selected dates.'])->withInput();
        }

        $booking->update([
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'guests' => $data['guests'],
            'special_requests' => $data['special_requests'] ?? null,
            'status' => 'confirmed',
        ]);

        Notification::send($booking->user, new BookingConfirmationNotification(
            $booking->fresh('room'),
            'updated',
            'Your booking schedule has been updated.'
        ));

        return redirect()->route('bookings.show', $booking)->with('status', 'Booking rescheduled.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('cancellation_reason'),
        ]);

        Notification::send($booking->user, new BookingConfirmationNotification(
            $booking->fresh('room'),
            'cancelled',
            'Your booking has been cancelled.'
        ));

        return redirect()->route('bookings.index')->with('status', 'Booking cancelled.');
    }

    public function feedback(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->status === 'cancelled' || now()->lessThan($booking->check_out_at)) {
            return back()->withErrors(['rating' => 'Feedback is available after the stay is complete.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        Feedback::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => $booking->user_id,
                'room_id' => $booking->room_id,
                'rating' => $data['rating'],
                'comments' => $data['comments'] ?? null,
                'is_public' => true,
            ]
        );

        return back()->with('status', 'Feedback submitted.');
    }

    private function validatedBooking(Request $request, int $capacity): array
    {
        return $request->validate([
            'check_in_at' => ['required', 'date'],
            'check_out_at' => ['required', 'date'],
            'guests' => ['required', 'integer', 'min:1', 'max:' . $capacity],
            'special_requests' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        if ($request->user()?->role === 'admin') {
            return;
        }

        abort_unless($request->user()?->id === $booking->user_id, 403);
    }
}
