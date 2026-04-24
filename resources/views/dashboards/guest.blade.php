@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="panel stack">
            <h1>Plan your next stay in a few clicks.</h1>
            <p>
                Browse available rooms, book dates and times, reschedule if plans change, and leave feedback after checkout.
            </p>
            <div class="row">
                <a class="button primary" href="{{ route('rooms.index') }}">Browse rooms</a>
                <a class="button" href="{{ route('bookings.index') }}">My bookings</a>
            </div>
            <div class="row pt-2">
                <a class="tab" href="{{ route('bookings.index') }}">My Bookings</a>
                <a class="tab" href="{{ route('rooms.index') }}">Reschedule</a>
                <a class="tab" href="{{ route('bookings.index') }}">Cancel Booking</a>
                <a class="tab" href="{{ route('bookings.index') }}">Feedback</a>
            </div>
        </div>
        <div class="panel grid cols-2">
            <div class="muted-box stat-card stat-card-blue">
                <h3>{{ $roomCount }}</h3>
                <p class="muted">Available rooms</p>
            </div>
            <div class="muted-box stat-card stat-card-cyan">
                <h3>{{ $bookingCount }}</h3>
                <p class="muted">Total bookings</p>
            </div>
            <div class="muted-box stat-card stat-card-red">
                <h3>{{ $feedbackCount }}</h3>
                <p class="muted">Feedback entries</p>
            </div>
            <div class="muted-box stat-card stat-card-indigo">
                <h3>{{ $upcomingBookings->count() }}</h3>
                <p class="muted">Upcoming stays</p>
            </div>
        </div>
    </section>

    <section class="section panel stack">
        <h2 class="text-center">Upcoming bookings</h2>
        @if ($upcomingBookings->isEmpty())
            <p class="muted mt-3">You do not have upcoming bookings yet.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($upcomingBookings as $booking)
                            <tr class="booking-card">
                                <td><a href="{{ route('bookings.show', $booking) }}">{{ $booking->reference }}</a></td>
                                <td>{{ $booking->room->name }}</td>
                                <td>{{ $booking->check_in_at->format('M j, Y g:i A') }}</td>
                                <td>{{ $booking->check_out_at->format('M j, Y g:i A') }}</td>
                                <td><span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
