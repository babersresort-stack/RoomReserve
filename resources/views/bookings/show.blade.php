@extends('layouts.app')

@section('content')
    <section class="section pb-0">
        <div class="row">
            @if ($isAdmin)
                <a class="button" href="{{ route('admin.bookings.index') }}">Back to bookings</a>
                <a class="button" href="{{ route('admin.dashboard') }}">Back to dashboard</a>
            @else
                <a class="button" href="{{ route('bookings.index') }}">Back to bookings</a>
                <a class="button" href="{{ route('dashboard') }}">Back to dashboard</a>
            @endif
        </div>
    </section>

    <section class="section grid cols-2">
        <article class="panel stack">
            <span class="pill">{{ $booking->reference }}</span>
            <h1>{{ $booking->room->name }}</h1>
            <p class="muted">Guest: {{ $booking->user->name }}</p>
            <span class="badge badge-{{ $booking->status }}">{{ $booking->status }}</span>
            <p>Check-in: {{ $booking->check_in_at->format('M j, Y g:i A') }}</p>
            <p>Check-out: {{ $booking->check_out_at->format('M j, Y g:i A') }}</p>
            <p>Guests: {{ $booking->guests }}</p>
            <p>Total bill: PHP {{ number_format($booking->total_bill, 2) }}</p>
            <p>
                Availability:
                <span class="badge badge-{{ $booking->availability_label === 'available' ? 'available' : 'unavailable' }}">
                    {{ ucfirst($booking->availability_label) }}
                </span>
            </p>
            <p>Special requests: {{ $booking->special_requests ?: 'None' }}</p>
            @if ($booking->cancelled_at)
                <p class="muted">Cancelled at {{ $booking->cancelled_at->format('M j, Y g:i A') }}</p>
            @endif
            @if ($isAdmin)
                <a class="button" href="{{ route('admin.rooms.edit', $booking->room) }}">Edit room</a>
            @endif
        </article>

        <div class="stack">
            @if (! $isAdmin && $booking->status !== 'cancelled')
                <form class="panel stack" method="POST" action="{{ route('bookings.update', $booking) }}">
                    @csrf
                    @method('PATCH')
                    <h2>Reschedule booking</h2>
                    <div>
                        <label for="check_in_at">Check-in</label>
                        <input id="check_in_at" name="check_in_at" type="datetime-local" value="{{ old('check_in_at', $booking->check_in_at->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div>
                        <label for="check_out_at">Check-out</label>
                        <input id="check_out_at" name="check_out_at" type="datetime-local" value="{{ old('check_out_at', $booking->check_out_at->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div>
                        <label for="guests">Guests</label>
                        <input id="guests" name="guests" type="number" min="1" max="{{ $booking->room->capacity }}" value="{{ old('guests', $booking->guests) }}" required>
                    </div>
                    <div>
                        <label for="special_requests">Special requests</label>
                        <textarea id="special_requests" name="special_requests">{{ old('special_requests', $booking->special_requests) }}</textarea>
                    </div>
                    <button class="button primary" type="submit">Save changes</button>
                </form>

                <form class="panel stack" method="POST" action="{{ route('bookings.cancel', $booking) }}">
                    @csrf
                    <h2>Cancel booking</h2>
                    <div>
                        <label for="cancellation_reason">Reason</label>
                        <textarea id="cancellation_reason" name="cancellation_reason"></textarea>
                    </div>
                    <button class="button danger" type="submit">Cancel reservation</button>
                </form>
            @endif

            @if (! $isAdmin && $canReview)
                <form class="panel stack" method="POST" action="{{ route('bookings.feedback', $booking) }}">
                    @csrf
                    <h2>Leave feedback</h2>
                    <div>
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" required>
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}">{{ $rating }} stars</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="comments">Comments</label>
                        <textarea id="comments" name="comments"></textarea>
                    </div>
                    <button class="button primary" type="submit">Submit feedback</button>
                </form>
            @elseif ($booking->feedback)
                <div class="panel stack">
                    <h2>Your feedback</h2>
                    <p class="muted">{{ $booking->feedback->rating }}/5</p>
                    <p>{{ $booking->feedback->comments }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
